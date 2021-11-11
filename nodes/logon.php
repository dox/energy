<div class="container px-4 py-5 form-signin">
  <form method="post" id="loginSubmit" action="index.php">
    <div class="mb-4 text-center">
      <svg width="4em" height="4em">
        <use xlink:href="inc/icons.svg#logo"/>
      </svg>

      <h1 class="h3 mb-3 font-weight-normal">Please Log In</h1>
    </div>
    <label for="inputUsername" class="visually-hidden">Username</label>
    <input type="text" id="inputUsername" name="inputUsername" class="form-control" placeholder="Username" required autofocus>
    <label for="inputPassword" class="visually-hidden">Password</label>
    <input type="password" id="inputPassword" name="inputPassword" class="form-control" placeholder="Password" required>
    <button class="btn btn-lg btn-primary w-100" type="submit">Sign in</button>
    <p class="mt-5 mb-3 text-muted text-center"><a href="<?php echo reset_url; ?>">Forgot your password?</a></p>
  </form>
</div>

<style>
.form-signin {
  width: 100%;
  max-width: 330px;
  padding: 15px;
  margin: auto;
}
.form-signin .checkbox {
  font-weight: 400;
}
.form-signin .form-control {
  position: relative;
  box-sizing: border-box;
  height: auto;
  padding: 10px;
  font-size: 16px;
}
.form-signin .form-control:focus {
  z-index: 2;
}
.form-signin input[type="text"] {
  margin-bottom: -1px;
  border-bottom-right-radius: 0;
  border-bottom-left-radius: 0;
}
.form-signin input[type="password"] {
  margin-bottom: 10px;
  border-top-left-radius: 0;
  border-top-right-radius: 0;
}
</style>
