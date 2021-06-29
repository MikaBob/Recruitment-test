<?php

namespace Blexr\Controller;

use \Twig\Loader\FilesystemLoader as Twig_Loader_Filesystem;
use \Twig\Environment as Twig_Environment;
use \Twig\Extra\Intl\IntlExtension;

// Mother class for controller which generate a view (which use twig)
abstract class DefaultController {

    protected $twig;

    public function __construct() {
        $this->loadTwig();
    }

    public function loadTwig() {
        $loader = new Twig_Loader_Filesystem(["./src/View"]);

        // set up environment
        $params = array(
            'cache' => "../../tmp/cache",
            'auto_reload' => true
        );

        $this->twig = new Twig_Environment($loader, $params);
        $this->twig->addExtension(new IntlExtension());

        // Global var for twig. Usefull to check user rights and show info
        $loggedUser = $_SERVER['loggedUser'] ?? null;
        $this->twig->addGlobal('loggedUser', $loggedUser);
        $this->twig->addGlobal('isAdmin', AuthenticationController::isAdmin());
    }

}
