# AGENTS.md

## Project overview

This repository is a Laravel 11 application for PT Semen Tonasa's K3 work-permit (SIK) process. It serves five access contexts:

- `user`: external vendor/contractor; follows the full 13-step submission flow.
- `pgo`: internal employee; follows only notification/OP/SPK, JSA, and working-permit steps.
- `admin`: K3 reviewer; claims submissions and reviews each step.
- `Super Admin`: manages users/roles and signs the final SIK.
- unauthenticated token holder: may edit one specific contractor/JSA/permit form and view its PDF while its bearer token is valid.

The stack is PHP 8.2+, Laravel 11, Blade, Tailwind CSS 3, Alpine.js, Vite 5, Signature Pad, and DomPDF. The default and test database is SQLite; production may use MySQL, so database logic must remain portable.

## Start here

Before changing a workflow, inspect all of these together:

- `routes/web.php` for middleware, route names, and token routes.
- `app/Http/Controllers/Controller.php` for ownership, token expiry, PDF access, and signature helpers.
- `app/Http/Controllers/User/IzinKerjaController.php` and `IzinKerjaPgoController.php` for user-facing step state.
- `app/Http/Controllers/Admin/AdminPermintaanController.php` for admin review and assignment.
- `app/Http/Controllers/Admin/ApproveSikController.php` for final signatures.
- `app/Models/Notification.php`, `StepApproval.php`, `Upload.php`, and the relevant permit model.
- The matching migration, modal/form Blade, token Blade, and PDF Blade.

Do not assume two permit types behave identically. Their fields, JSON encoding, signatures, detail/closure data, and PDF layouts differ.

## Domain model and flow

`Notification` is the aggregate root for a submission. It owns or identifies uploads, contractor data, JSA, permit records, approval rows, and the assigned admin.

### Vendor/user flow

The canonical backend step keys, in order, are:

1. `op_spk`
2. `data_kontraktor`
3. `bpjs`
4. `jsa`
5. `working_permit`
6. `fakta_integritas`
7. `sertifikasi_ak3`
8. `ktp`
9. `surat_kesehatan`
10. `struktur_organisasi`
11. `post_test`
12. `bukti_serah_terima`
13. `sik`

The user dashboard currently calls the last display step `surat_izin_kerja`, but its persisted approval key is `sik`. Treat this as a compatibility alias; do not introduce another name or rename it in only one layer.

Steps are sequential. A following step becomes available only after the previous `StepApproval` is `disetujui`. A submitted form/file is not the same thing as approval.

### PGO flow

PGO uses only `op_spk`, `jsa`, and `working_permit`. PGO must not gain contractor-data or general-document upload access. When adding status or dashboard logic, compute totals from the usertype-specific step list rather than assuming 13 steps.

### Admin and SIK flow

- Opening an unassigned request detail auto-assigns it to the first admin who opens it.
- Only `assigned_admin_id` may update statuses or delete submitted data for that request.
- `StepApproval.status` values are `menunggu`, `perlu_disetujui`, `disetujui`, and `revisi`.
- The user UI maps those values to `pending`, `done`, and `revisi`; do not persist UI-only labels.
- Final signatures live on the `sik` `StepApproval` row as `signature_senior_manager` and `signature_manager`.
- A final signature may only be stored after the `sik` step has status `disetujui`.

`Notification.status`, `Upload.status`, and `StepApproval.status` are separate concepts. For workflow progress, `StepApproval` is authoritative. Do not silently synchronize or substitute one for another without a migration plan and tests.

### Working permits

Supported permit keys are:

- `umum`
- `gaspanas`
- `air`
- `ketinggian`
- `pengangkatan`
- `penggalian`
- `risiko-panas`
- `ruang-tertutup` in user/token routing; some admin maps use legacy `ruangtertutup`
- `beban`
- `perancah`
- `listrik-tegangan-tinggi` (permit khusus; `umum` harus sudah tersedia sebelum permit ini dibuat)

Most permits have one permit record per notification and may share `WorkPermitDetail` plus `WorkPermitClosure`, distinguished by `permit_type`. Preserve the key expected by routes, controllers, models, Blade components, admin maps, and PDFs. If normalizing `ruang-tertutup`/`ruangtertutup`, update every layer and add compatibility coverage.

## Authorization and token security

Authorization is a required invariant, not a view concern.

- Authenticated user/PGO reads and writes must scope the notification to `auth()->id()`. Reuse `findAccessibleNotification()` or a policy.
- Admin-only and Super-Admin-only actions must remain behind their existing `auth`, `verified`, `usertype`, and `role` middleware.
- Never authorize from a hidden field, request boolean, `notification_id`, model ID, or a disabled button. In particular, do not trust client-supplied `_token_access`; token access must be established server-side by resolving the token route's record.
- A token request must resolve the record by its UUID token, call `abortIfPermitTokenExpired()`, and derive `notification_id` from that resolved record. Never accept a replacement notification ID from the request.
- Token regeneration must remain owner/admin scoped and must rotate both `token` and `token_expires_at`.
- Token PDFs deliberately use the request attribute `token_pdf_access`; set it only after resolving and validating a non-expired token.
- Keep public token routes throttled. Tokens are bearer credentials and must never be written to logs.
- Any route that accepts a submission ID must be checked for horizontal access (IDOR), including PDF routes.

When touching authorization, add feature tests for owner success, different-user denial, wrong-role denial, expired-token denial, and valid-token success.

## Data and persistence rules

- Use validation before storage. Prefer dedicated Form Requests when a form is being substantially changed.
- Use transactions when one action writes a permit, `WorkPermitDetail`, `WorkPermitClosure`, approval state, or stored files as one logical unit.
- `updateOrCreate()` expresses one-record-per-notification intent, but several tables do not enforce that intent with a database unique constraint. Do not rely on it for concurrency safety; add a migration before depending on uniqueness.
- Add schema changes in a new migration. Never edit an already-deployed migration merely to change production schema.
- Keep migrations compatible with SQLite tests and MySQL production. Avoid vendor-specific SQL unless guarded and covered.
- JSON fields should be PHP arrays at the model boundary and declared in `$casts`. Avoid double `json_encode()` when a field already has an `array` cast. Preserve explicit legacy decoding only where old double-encoded data is known to exist.
- Use `array_filter` carefully: filtering only `null`/empty string is intentional because `false`, `0`, and empty arrays may be meaningful permit answers.
- JSA numbers follow `{sequence}/JSA/ST/{mmyyyy}`, use the numeric maximum for the current month, and have a global unique index. Preserve retry/concurrency behavior and SQLite compatibility.
- Store dates/times consistently with the existing column types and add casts for dates/timestamps used as objects.

## Files, signatures, and PDFs

- User uploads and signatures belong on the `public` disk. Database paths are generally stored without a leading slash; signature paths commonly start with `storage/`.
- Use Laravel `Storage` APIs instead of direct `file_put_contents()`/`mkdir()` in new code.
- Validate MIME/type and size server-side. Base64 signatures must be strict PNG data URLs, strictly decoded, bounded in size, image-validated, and verified after writing.
- When replacing or deleting a record, remove its old physical files. Database cascade deletion does not remove filesystem objects.
- Preserve an existing signature when an edit submits no replacement, unless the request explicitly clears it.
- A form change is incomplete until its regular Blade, token Blade, controller validation/storage, model fillable/casts, migration, preview controller, and PDF Blade agree on field names.
- DomPDF templates should use simple, deterministic HTML/CSS and storage paths that work outside a browser session. Test that responses begin with `%PDF-` where practical.
- After a fresh setup, `php artisan storage:link` is required for browser-visible public files.

## Views and frontend

- The primary dashboards are `resources/views/pengajuan-user/izin-kerja.blade.php` and `izin-kerja-pgo.blade.php`.
- Reusable modal forms live under `resources/views/components/steps/`; permit components live under `resources/views/components/permits/`.
- Public token forms and PDF templates live under `resources/views/pengajuan-user/`.
- Keep Alpine state local and keep form field names aligned exactly with controller validation.
- Avoid new Eloquent queries inside Blade. Load approval notes, uploads, permits, and relationships in controllers to prevent N+1 queries and to keep authorization auditable.
- Do not make workflow enforcement depend only on a disabled/hidden UI control; enforce it again in the controller/domain layer.
- When editing Tailwind classes or adding Blade files, ensure the path is covered by `tailwind.config.js` and run a production asset build.

## Code conventions

- Follow Laravel conventions and PSR-12. Format touched PHP with `vendor/bin/pint`.
- Keep controllers focused. Shared permit concerns belong in the base controller only when semantics really match every permit; otherwise use a dedicated service/action.
- Prefer route model binding or explicitly scoped queries over unscoped `findOrFail()` for user-owned resources.
- Centralize shared step definitions and permit maps when refactoring; currently they are duplicated across user dashboards and admin controllers, so partial edits are risky.
- Use enums/constants for new status/step logic rather than adding new string literals throughout the codebase.
- Preserve existing Indonesian user-facing terminology unless the task explicitly changes copy.
- Do not expose exception messages to end users. Log diagnostic context without tokens, signatures, personal documents, or other sensitive K3/personnel data.
- Do not modify generated/dependency directories (`vendor/`, `node_modules/`, `public/build/`) by hand.

## Testing and verification

For a normal backend change, run:

```bash
composer install
php artisan migrate:fresh --env=testing
php artisan test
vendor/bin/pint --test
```

For view/CSS/JS changes, also run:

```bash
npm ci
npm run build
```

Useful focused commands:

```bash
php artisan test --filter=JsaNumberingTest
php artisan test --filter=JsaSignatureTest
php artisan test --filter=DataKontraktorTokenFormTest
php artisan route:list --except-vendor
```

Tests use in-memory SQLite via `phpunit.xml`. Use `RefreshDatabase`, model factories where possible, `Storage::fake('public')` for file behavior, and explicit actor setup (`actingAs`) for authorization.

Minimum regression coverage by change type:

- route/controller authorization: allowed and forbidden actors;
- token flow: valid, expired, regenerated/old token, and unrelated record;
- workflow: sequential lock, revision, approval, and PGO-vs-user totals;
- permit form: authenticated save, token save, JSON round trip, signature preservation, PDF response;
- destructive action: assigned-admin restriction, database deletion, file deletion, and approval reset;
- migration: clean `migrate:fresh` on SQLite plus production-dialect review.

If PHP or Node is unavailable in the environment, state exactly which commands could not run; do not claim successful verification.

## Known sharp edges to respect or improve deliberately

- The authenticated final-SIK PDF route currently reaches `AdminPermintaanController::viewSik()`; every non-admin caller must be ownership-checked and final-status-checked before content is returned.
- Several permit `store()` methods skip ownership when request input `_token_access` is truthy. Do not copy this pattern. Replace it with server-derived token context when working in that area.
- Step definitions, permit maps, and progress calculation are duplicated and occasionally use different aliases/order. A change must update all consumers or first centralize them with regression tests.
- The full user Blade performs many database queries while rendering. Move touched queries into eager-loaded controller data rather than adding more.
- `JsaController::edit()` references `pengajuan-user.jsa.edit`, which is not currently present; verify the actual modal-based edit path before relying on that route.
- Model/database cascades do not consistently clean stored upload/signature files.
- Database uniqueness does not consistently enforce one contractor/JSA/permit/approval record per logical key.
- `README.md` setup documentation is incomplete; use this file and the actual configuration as the operational source until it is repaired.

Do not fix unrelated sharp edges opportunistically in a narrowly scoped task. Mention them when relevant, keep the requested change focused, and add tests around any edge you intentionally change.

## Completion checklist

Before handing off a change:

1. Trace the affected actor and both authenticated/token paths.
2. Confirm canonical step, permit, status, and field names across every layer.
3. Re-check ownership, role middleware, assigned-admin rules, and token expiry.
4. Verify database records and physical files stay consistent on create/update/delete.
5. Run the smallest focused tests plus the full suite when available.
6. Run Pint and the frontend production build when applicable.
7. Report changed files, behavior, tests run, and any commands blocked by the environment.
