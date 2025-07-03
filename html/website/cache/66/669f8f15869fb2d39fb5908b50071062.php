<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* navbar.twig */
class __TwigTemplate_89bf2aaefc77c7ef218647f295bcfa3e extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 1
        yield "<header>
    <a href=\"index.php#home\">
        <img src=\"images/logos/Lancaster's-logos_white_cropped.png\" alt=\"Home Page logo\" style=\"height: 60px;\">
    </a>
    <nav>
        <a href=\"index.php\">Home</a>
        <a href=\"index.php#about\">About Us</a>
        <a href=\"menu.php\">Menu</a>
        <a href=\"booktable.php\">Book a Table</a>
        <a href=\"index.php#gallery\">Gallery</a>
    </nav>
    <div class=\"nav-right\">
        ";
        // line 13
        if (($context["isLoggedIn"] ?? null)) {
            // line 14
            yield "            ";
            if (($context["isStaff"] ?? null)) {
                // line 15
                yield "                <button onclick=\"window.location.href='staffdashboard.php'\">Staff Dashboard</button>
            ";
            } else {
                // line 17
                yield "                <button onclick=\"window.location.href='myaccount.php'\">My Account</button>
            ";
            }
            // line 19
            yield "        ";
        } else {
            // line 20
            yield "            <button onclick=\"window.location.href='login.php?section=login'\">Login</button>
        ";
        }
        // line 22
        yield "    </div>
</header>
";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "navbar.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  76 => 22,  72 => 20,  69 => 19,  65 => 17,  61 => 15,  58 => 14,  56 => 13,  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<header>
    <a href=\"index.php#home\">
        <img src=\"images/logos/Lancaster's-logos_white_cropped.png\" alt=\"Home Page logo\" style=\"height: 60px;\">
    </a>
    <nav>
        <a href=\"index.php\">Home</a>
        <a href=\"index.php#about\">About Us</a>
        <a href=\"menu.php\">Menu</a>
        <a href=\"booktable.php\">Book a Table</a>
        <a href=\"index.php#gallery\">Gallery</a>
    </nav>
    <div class=\"nav-right\">
        {% if isLoggedIn %}
            {% if isStaff %}
                <button onclick=\"window.location.href='staffdashboard.php'\">Staff Dashboard</button>
            {% else %}
                <button onclick=\"window.location.href='myaccount.php'\">My Account</button>
            {% endif %}
        {% else %}
            <button onclick=\"window.location.href='login.php?section=login'\">Login</button>
        {% endif %}
    </div>
</header>
", "navbar.twig", "/var/www/html/website/templates/navbar.twig");
    }
}
