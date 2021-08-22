
<?php
try {
    $path = self::UI_PATH.'global/guest_header.php';
    (file_exists($path))
        ? require_once $path
        : throw new Exception("<strong style='color: #ef661b'>$path</strong> is not exist.");
} catch (Exception $e) {
    die($e->getMessage());
}
?>

<main class="main-content  mt-0">
    <section>
        <div class="page-header min-vh-75">
            <div class="container">
                <div class="row">
                    <div class="col-xl-4 col-lg-5 col-md-6 d-flex flex-column mx-auto">
                        <div class="card card-plain mt-8">
                            <div class="card-header pb-0 text-left bg-transparent">
                                <h3 class="font-weight-bolder text-info text-gradient">Welcome to <?= APP['INFO']['name'] ?></h3>
                                <p class="mb-0">Enter your detail to register.</p>
                            </div>
                            <div class="card-body">
                                <form id="register">
                                    <label for="email">Email</label>
                                    <div class="mb-3">
                                        <input id="email" name="email" type="email" class="form-control" placeholder="Email">
                                    </div>
                                    <label for="first-name">First Name</label>
                                    <div class="mb-3">
                                        <input id="first-name" name="first-name" type="text" class="form-control" placeholder="First Name">
                                    </div>
                                    <label for="last-name">Last Name</label>
                                    <div class="mb-3">
                                        <input id="last-name" name="last-name" type="text" class="form-control" placeholder="Last Name">
                                    </div>
                                    <label for="country">Country</label>
                                    <div class="mb-3">
                                        <select id="country" name="country" class="form-control" placeholder="Country">
                                            <?php foreach ($this->Page_DATA->countries as $code=>$country) { ?>
                                                <option value="<?= $code ?>"><?= $country ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <label for="city">City</label>
                                    <div class="mb-3">
                                        <input id="city" name="city" type="text" class="form-control" placeholder="City">
                                    </div>
                                    <div class="text-center">
                                        <button type="button" class="btn bg-gradient-info w-100 mt-4 mb-0">Sign up</button>
                                    </div>
                                </form>
                                <div class="cb-hide alert alert-success text-light" id="alert-register">
                                    <strong>Danger!</strong> This is a danger alert—check it out!
                                </div>
                                <div class="cb-hide alert alert-danger text-light" id="alert-register">
                                    <strong>Danger!</strong> This is a danger alert—check it out!
                                </div>
                            </div>
                            <div class="card-footer text-center pt-0 px-lg-2 px-1">
                                <p class="text-sm mt-3 mb-0">Already have an account?
                                    <a href="login" class="text-dark font-weight-bolder">Sign in</a>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="oblique position-absolute top-0 h-100 d-md-block d-none me-n8">
                            <div class="oblique-image bg-cover position-absolute fixed-top ms-auto h-100 z-index-0 ms-n6" style="background-image:url('<?= ASSETS_PATH ?>img/register.png')"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>


<?php
try {
    $path = self::UI_PATH.'global/guest_footer.php';
    (file_exists($path))
        ? require_once $path
        : throw new Exception("<strong style='color: #ef661b'>$path</strong> is not exist.");
} catch (Exception $e) {
    die($e->getMessage());
}
?>

<!--   Core JS Files   -->
<script src="<?= ASSETS_PATH ?>js/popper.min.js"></script>
<script src="<?= ASSETS_PATH ?>js/bootstrap.min.js"></script>
<script src="<?= ASSETS_PATH ?>js/perfect-scrollbar.min.js"></script>
<script src="<?= ASSETS_PATH ?>js/smooth-scrollbar.min.js"></script>
<script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
        var options = {
            damping: '0.5'
        }
        Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
</script>
<!-- Github buttons -->
<script async defer src="https://buttons.github.io/buttons.js"></script>
<!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
<script src="<?= ASSETS_PATH ?>js/soft-ui-dashboard.min.js?v=1.0.3"></script>
</body>

</html>