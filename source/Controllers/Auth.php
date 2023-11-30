<?php

namespace Source\Controllers;

use League\OAuth2\Client\Provider\Facebook;
use League\OAuth2\Client\Provider\FacebookUser;
use League\OAuth2\Client\Provider\Google;
use League\OAuth2\Client\Provider\GoogleUser;
use Source\Models\User;
use Source\Support\Email;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


/**
 * Classe para autenticação.
 * Class Auth
 * @package Source\Controllers
 */
class Auth extends Controller
{

  /**
   * @param Router $router
   * @return void
   */
  public function __construct($router)
  {
    parent::__construct($router);
  }

  /**
   * @param array $data
   * @return void
   */
  public function login(array $data): void
  {
    $email = filter_var($data["email"], FILTER_VALIDATE_EMAIL);
    $passwd = filter_var($data["passwd"], FILTER_DEFAULT);

    if (!$email || !$passwd) {
      echo $this->ajaxResponse("message", [
        "type" => "alert",
        "message" => "Dados inválidos. Informe E-mail e senha corretamente para efetuar o login."
      ]);
      return;
    }

    $user = (new User())->find("email = :e", "e={$email}")->fetch();

    if (!$user || !password_verify($passwd, $user->passwd)) {
      echo $this->ajaxResponse("message", [
        "type" => "error",
        "message" => "E-mail ou senha informados não conferem."
      ]);
      return;
    }

    /** SOCIAL VALIDATE */
    $this->socialValidate($user);

    $_SESSION["user"] = $user->id;
    echo $this->ajaxResponse("redirect", [
      "url" => $this->router->route("app.home")
    ]);
  }

  /**
   * @param array $data
   * @return void
   */
  public function register(array $data): void
  {
    $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
    if (in_array("", $data)) {
      echo $this->ajaxResponse("message", [
        "type" => "error",
        "message" => "Preencha todos os campos para cadastrar-se."
      ]);
      return;
    }

    $user = new User();
    $user->first_name = $data["first_name"];
    $user->last_name = $data["last_name"];
    $user->email = $data["email"];
    $user->passwd = $data["passwd"];

    /** SOCIAL VALIDATE */
    $this->socialValidate($user);

    if (!$user->save()) {
      echo $this->ajaxResponse("message", [
        "type" => "error",
        "message" => $user->fail()->getMessage()
      ]);
      return;
    }

    $_SESSION["user"] = $user->id;

    echo $this->ajaxResponse("redirect", [
      "url" => $this->router->route("app.home")
    ]);
  }

  /**
   * @param array $data
   * @return void
   */
  public function forget(array $data): void
  {
    $email = filter_var($data["email"], FILTER_VALIDATE_EMAIL);
    if (!$email) {
      echo $this->ajaxResponse("message", [
        "type" => "alert",
        "message" => "Informe o seu E-mail para recuperar a senha."
      ]);
      return;
    }

    $user = (new User())->find("email = :e", "e={$email}")->fetch();
    if (!$user) {
      echo $this->ajaxResponse("message", [
        "type" => "error",
        "message" => "O E-mail informado não é cadastrado."
      ]);
      return;
    }

    $user->forget = (md5(uniqid(rand(), true)));
    $user->save();

    $_SESSION["forget"] = $user->id;

    $email = new Email;
    $email->add(
      "Recupere sua senha || " . site("name"),
      $this->view->render("emails/recover", [
        "user" => $user,
        "link" => $this->router->route("web.reset", [
          "email" => $user->email,
          "forget" => $user->forget
        ])
      ]),
      "{$user->first_name} {$user->last_name}",
      $user->email
    )->send();

    flash("success", "Enviamos um link de recuperação para seu E-mail");

    echo $this->ajaxResponse("redirect", [
      "url" => $this->router->route("web.login")
    ]);
  }

  /**
   * @param array $data
   * @return void
   */
  public function reset(array $data): void
  {
    if (empty($_SESSION["forget"]) || !$user = (new User())->findById($_SESSION["forget"])) {
      flash("error", "Não foi possível recuperar, tente novamente");
      echo $this->ajaxResponse("redirect", [
        "url" => $this->router->route("web.forget")
      ]);
      return;
    }

    if (empty($data["password"]) || empty($data["password_re"])) {
      echo $this->ajaxResponse("message", [
        "type" => "alert",
        "message" => "Informe e repita sua nova senha"
      ]);
      return;
    }

    if ($data["password"] != $data["password_re"]) {
      echo $this->ajaxResponse("message", [
        "type" => "error",
        "message" => "Você informou duas senhas diferentes"
      ]);
      return;
    }

    $user->passwd = $data["password"];
    $user->forget = null;

    if (!$user->save()) {
      echo $this->ajaxResponse("message", [
        "type" => "error",
        "message" => $user->fail()->getMessage()
      ]);
      return;
    }

    unset($_SESSION["forget"]);

    $email = new Email;
    $email->add(
      "Senha Atualizada Recentemente || " . site("name"),
      $this->view->render("emails/recover_sucess", [
        "user" => $user,
        "link" => $this->router->route("web.forget")
      ]),
      "{$user->first_name} {$user->last_name}",
      $user->email
    )->send();

    flash("success", "Senha atualizada com sucesso!");
    echo $this->ajaxResponse("redirect", [
      "url" => $this->router->route("web.login")
    ]);
    return;
  }

  /**
   * @return void
   */
  public function facebook(): void
  {
    $facebook = new Facebook(FACEBOOK_LOGIN);
    echo '<pre>';
    var_dump(FACEBOOK_LOGIN);
    echo '</pre>';
    exit();
    $error = filter_input(INPUT_GET, "error", FILTER_SANITIZE_STRIPPED);
    $code = filter_input(INPUT_GET, "code", FILTER_SANITIZE_STRIPPED);

    if (!$error && !$code) {
      $auth_url = $facebook->getAuthorizationUrl(["scope" => "email"]);
      header("Location: {$auth_url}");
      return;
    }

    if ($error) {
      flash("error", "Não foi possível logar com o Facebook");
      $this->router->redirect("web.login");
    }

    if ($code && empty($_SESSION["facebook_auth"])) {
      try {
        $token = $facebook->getAccessToken("authorization_code", ["code" => $code]);
        $_SESSION["facebook_auth"] = serialize($facebook->getResourceOwner($token));
      } catch (\Exception $exception) {
        flash("error", "Não foi possível logar com o Facebook");
        $this->router->redirect("web.login");
      }
    }

    /** @var FacebookUser */
    $facebook_user = unserialize($_SESSION["facebook_auth"]);
    $user_by_id = (new User())->find("facebook_id = :face_id", "face_id={$facebook_user->getId()}")->fetch();

    //LOGIN BY id FACEBOOK
    if ($user_by_id) {
      unset($_SESSION["facebook_auth"]);
      $_SESSION["user"] = $user_by_id->id;
      $this->router->redirect("app.home");
    }

    //LOGIN BY EMAIL
    $user_by_email = (new User())->find("email = :e", "e={$facebook_user->getEmail()}")->fetch();

    if ($user_by_email) {
      flash("info", "Olá {$facebook_user->getFirstName()}, faça login para conectar seu Facebook.");
      $this->router->redirect("web.login");
    }

    //REGISTER OF NOT
    $link = $this->router->route("web.login");
    flash(
      "info",
      "Olá {$facebook_user->getFirstName()}, <b>se ja tem uma conta clique em <a title='Fazer Login' href='{$link}'>FAZER LOGIN</a></b>, ou complete seu cadastro."
    );
    $this->router->redirect("web.register");
  }

  /**
   * @return void
   */
  public function google(): void
  {
    $google = new Google(GOOGLE_LOGIN);
    $error = filter_input(INPUT_GET, "error", FILTER_SANITIZE_STRIPPED);
    $code = filter_input(INPUT_GET, "code", FILTER_SANITIZE_STRIPPED);

    if (!$error && !$code) {
      $auth_url = $google->getAuthorizationUrl();
      header("Location: {$auth_url}");
      return;
    }

    if ($error) {
      flash("error", "Não foi possível logar com o Google");
      $this->router->redirect("web.login");
    }

    if ($code && empty($_SESSION["google_auth"])) {
      try {
        $token = $google->getAccessToken("authorization_code", ["code" => $code]);
        $_SESSION["google_auth"] = serialize($google->getResourceOwner($token));
      } catch (\Exception $exception) {
        flash("error", "Não foi possível logar com o google");
        $this->router->redirect("web.login");
      }
    }

    /** @var GoogleUser */
    $google_user = unserialize($_SESSION["google_auth"]);
    $user_by_id = (new User())->find("google_id = :g_id", "g_id={$google_user->getId()}")->fetch();

    //LOGIN BY id google
    if ($user_by_id) {
      unset($_SESSION["google_auth"]);
      $_SESSION["user"] = $user_by_id->id;
      $this->router->redirect("app.home");
    }

    //LOGIN BY EMAIL
    $user_by_email = (new User())->find("email = :e", "e={$google_user->getEmail()}")->fetch();

    if ($user_by_email) {
      flash("info", "Olá {$google_user->getFirstName()}, faça login para conectar seu Google.");
      $this->router->redirect("web.login");
    }

    //REGISTER OF NOT
    $link = $this->router->route("web.login");
    flash(
      "info",
      "Olá {$google_user->getFirstName()}, <b>se ja tem uma conta clique em <a title='Fazer Login' href='{$link}'>FAZER LOGIN</a></b>, ou complete seu cadastro."
    );
    $this->router->redirect("web.register");
  }

  /**
   * @param User $user
   * @return void
   */
  public function socialValidate(User $user): void
  {
    /**
     * FACEBOOK
     */
    if (!empty($_SESSION["facebook_auth"])) {
      /** @var $facebook_user FacebookUser */
      $facebook_user = unserialize($_SESSION["facebook_auth"]);

      $user->facebook_id = $facebook_user->getId();
      $user->photo = $facebook_user->getPictureUrl();
      $user->save();

      unset($_SESSION["facebook_auth"]);
    }

    /**
     * GOOGLE
     */
    if (!empty($_SESSION["google_auth"])) {
      /** @var GoogleUser */
      $google_user = unserialize($_SESSION["google_auth"]);

      $user->google_id = $google_user->getId();
      $user->photo = $google_user->getAvatar();
      $user->save();

      unset($_SESSION["google_auth"]);
    }
  }
}
