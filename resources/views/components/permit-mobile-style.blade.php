<style>
@media (max-width: 640px) {
    .wp-permit-modal {
        align-items: flex-start !important;
        overflow: hidden !important;
        padding: .5rem !important;
    }

    .wp-permit-modal > .wp-permit-form {
        width: 100% !important;
        max-width: 100% !important;
        max-height: calc(100dvh - 1rem) !important;
        box-sizing: border-box;
        padding: .75rem !important;
    }

    .wp-permit-form {
        font-size: .72rem;
    }

    .wp-permit-form .p-6,
    .wp-permit-form .p-4 {
        padding: .55rem !important;
    }

    .wp-permit-form h2 {
        font-size: 1rem !important;
        line-height: 1.25 !important;
    }

    .wp-permit-form h3 {
        font-size: .9rem !important;
        line-height: 1.25 !important;
    }

    .wp-permit-form h4,
    .wp-permit-form p,
    .wp-permit-form label {
        font-size: .72rem !important;
        line-height: 1.25 !important;
    }

    .wp-permit-form table {
        width: 100% !important;
        max-width: 100% !important;
        table-layout: fixed !important;
        font-size: .56rem !important;
    }

    .wp-permit-form th,
    .wp-permit-form td {
        padding: .16rem .18rem !important;
        overflow-wrap: anywhere;
        word-break: break-word;
        vertical-align: middle;
    }

    .wp-permit-form input,
    .wp-permit-form select,
    .wp-permit-form textarea {
        width: 100% !important;
        min-width: 0 !important;
        max-width: 100% !important;
        padding: .24rem .28rem !important;
        font-size: .6rem !important;
        line-height: 1.15 !important;
    }

    .wp-permit-form input[type="checkbox"],
    .wp-permit-form input[type="radio"] {
        width: .75rem !important;
        height: .75rem !important;
        padding: 0 !important;
    }

    .wp-permit-form img[alt*="Tanda Tangan"],
    .wp-permit-form td img {
        max-height: 2rem !important;
        height: 2rem !important;
    }

    .wp-permit-form .overflow-x-auto {
        overflow-x: visible !important;
    }

    .wp-signature-button {
        position: relative;
        width: 1.85rem !important;
        min-width: 1.85rem !important;
        max-width: 1.85rem !important;
        height: 1.85rem !important;
        padding: 0 !important;
        font-size: 0 !important;
        line-height: 0 !important;
        overflow: hidden;
        border-radius: .45rem !important;
    }

    .wp-signature-button::before {
        content: "\270E";
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        font-size: .95rem;
        line-height: 1;
    }

    #signPadModal {
        align-items: flex-start !important;
        padding: .75rem !important;
    }

    #signPadModal > div {
        width: 100% !important;
        max-width: calc(100vw - 1.5rem) !important;
        max-height: calc(100dvh - 1.5rem) !important;
        overflow-y: auto !important;
        padding: .875rem !important;
        margin-top: .25rem;
        border-radius: .75rem !important;
        box-sizing: border-box;
    }

    #signaturePad {
        height: min(40dvh, 12rem) !important;
        min-height: 8.5rem;
    }

    #signPadModal h2 {
        margin-bottom: .5rem !important;
        font-size: .95rem !important;
        line-height: 1.25 !important;
    }

    #signPadModal .mt-4 {
        margin-top: .75rem !important;
    }

    #signPadModal button {
        font-size: .8rem !important;
        line-height: 1.2 !important;
    }
}

@media (max-width: 640px) and (max-height: 520px) {
    #signaturePad {
        height: 8rem !important;
        min-height: 8rem;
    }
}
</style>
