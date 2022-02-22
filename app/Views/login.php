<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>
    <div class="app app-auth-sign-in align-content-stretch d-flex flex-wrap justify-content-end">
        <div class="app-auth-background">

        </div>
        <div class="app-auth-container">
            <div class="logo">
                <a href="">Extra Takip</a>
            </div>
            <p class="auth-description">Paneli görüntüleyebilmek için giriş yapmanız gerekmektedeir.<br>Eğer hesabınız yoksa yönetici ile iletişime geçiniz.</p>
            <form action="" method="POST">
                <div class="auth-credentials m-b-xxl">
                    <?php if (isset($error) && !is_array($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php elseif (isset($error) && is_array($error)): ?>
                        <div class="alert alert-danger">
                            <ul class="list-unstyled" style="margin-bottom: 0">
                            <?php foreach ($error as $k => $v): ?>
                                <li><?= $v ?></li>
                            <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <label for="signInEmail" class="form-label">E-Posta Adresi</label>
                    <input type="email" name="email" class="form-control m-b-md" id="signInEmail" aria-describedby="signInEmail" placeholder="info@extratakip.com" value="<?= set_value('email') ?>">

                    <label for="signInPassword" class="form-label">Şifre</label>
                    <input type="password" name="password" class="form-control" id="signInPassword" aria-describedby="signInPassword" placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;">
                </div>

                <div class="auth-submit">
                    <button type="submit" class="btn btn-primary">Giriş Yap</button>
                    <?= csrf_field() ?>
                    <a href="#" class="auth-forgot-password float-end">Şifremi unuttum</a>
                </div>
            </form>
            <!-- <div class="divider"></div> -->
            <!-- <div class="auth-alts">
                <a href="#" class="auth-alts-google"></a>
                <a href="#" class="auth-alts-facebook"></a>
                <a href="#" class="auth-alts-twitter"></a>
            </div> -->
        </div>
    </div>
<?php $this->endsection('content'); ?>