<?php $root = $root ?? ''; $adminPagina = $adminPagina ?? false; ?>
</main>
<?php if (!$adminPagina): ?>
<footer class="site-footer">
    <div class="container footer-grid">
        <div>
            <a class="logo" href="index.php">⚽ Voetbaldagen <b>Horst</b></a>
            <p>30 augustus – 3 september 2027<br>Horst, Nederland</p>
        </div>
        <div>
            <h4>Website</h4>
            <a href="index.php">Home</a>
            <a href="toernooien.php">Toernooien</a>
            <a href="vrijwilligers.php">Vrijwilligers</a>
            <a href="contact.php">Contact</a>
            <a href="privacy.php">Privacy</a>
        </div>
        <div>
            <h4>Toernooien</h4>
            <a href="toernooien.php?t=voetvolley">Voetvolley</a>
            <a href="toernooien.php?t=champions-league">Champions League</a>
            <a href="toernooien.php?t=wk">Wereldkampioenschap</a>
        </div>
        <div>
            <h4>Contact</h4>
            <p>info@voetbaldagenhorst.nl<br>Telefoon: 000-0000000<br><small>(voorbeeldgegevens, nog aanpassen)</small></p>
            <a href="admin/login.php">Beheer</a>
        </div>
    </div>
    <div class="container copy">© 2027 Voetbaldagen Horst. Alle rechten voorbehouden.</div>
</footer>
<?php endif; ?>
<script src="<?= $root ?>assets/js/main.js"></script>
</body>
</html>