<div class="row d-flex justify-content-center">

    <!--Grid column-->
    <div class="col-md-6 margin-top-40">

        <?php if  (isset($data['error'])):?>
            <div class="alert alert-warning" role="alert">
                <?=$data['error'];?>
            </div>
        <?php endif;?>
        <form action="/main/login" method="post">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-8 col-sm-12 form-group">
                                <label for="InputLogin">Login</label>
                                <input type="text" class="form-control <?php if (isset($data['login'])) echo 'is-invalid';?>" id="InputLogin" name="login" placeholder="Login">
                                <div class="invalid-feedback">
                                    Required not empty value
                                </div>
                            </div>
                            <div class="col-md-8 col-sm-12 form-group">
                                <label for="InputPassword">Password</label>
                                <input type="password" class="form-control <?php if (isset($data['password'])) echo 'is-invalid';?>" id="InputPassword" name="password" placeholder="Password">
                                <div class="invalid-feedback">
                                    Required not empty value
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mb-2">Log In</button>
                        <a class="btn btn-primary mb-2" href="/task/index">Prev</a>
                    </div>
                </div>
            </div>
        </form>

    </div>
    <!--Grid column-->

</div>