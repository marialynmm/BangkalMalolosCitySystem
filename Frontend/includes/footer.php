<style>
    /* Main container to allow space for footer */
    .container {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    /* Content area that takes up available space */
    .content {
        flex: 1;
    }

    /* Footer styling */
    footer {
        color: #000000;
        text-align: center;
        padding: 1rem;
        width: 100%;
        /* Remove the sticky positioning */
        position: sticky;
        /* Ensure footer stays at the bottom if content is short */
        margin-top: auto;
        bottom: 0;
    }
</style>

<footer class="footer">
    <p>© 2024 Bangkal Malolos. All data is confidential and protected under privacy laws.</p>
</footer>