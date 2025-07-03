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

/* footer.twig */
class __TwigTemplate_f7284f9a3c584c785c29bb1ad28f03b7 extends Template
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
        yield "<footer role=\"contentinfo\">
    <div class=\"footer-container\">
        <div class=\"footer-section\">
            <h4>Opening Times</h4>
            <ul>
                <li>Mon – Fri: 07:30 am – 11 pm</li>
                <li>Sat: 9 am – 11 pm</li>
                <li>Sun: 11:30 am – 10 pm</li>
            </ul>
        </div>
        <div class=\"footer-section\">
            <h4>Address</h4>
            <address>
                52 Haymarket<br>
                London<br>
                SW1Y 4RP
            </address>
        </div>
        <div class=\"footer-logo\">
            <img src=\"images/logos/Lancaster's-logos_white_cropped.png\" alt=\"Lancaster's Restaurant Logo\">
        </div>
        <div class=\"footer-section\">
            <h4>Follow Us</h4>
            <nav aria-label=\"Social Media Links\">
                <ul class=\"social-links\">
                    <li><a href=\"https://www.instagram.com/fallowrestaurant\" target=\"_blank\" rel=\"noopener noreferrer\">Instagram</a></li>
                    <li><a href=\"https://www.youtube.com/channel/UCJ901NqoRaXMnIm7aOjLyuA\" target=\"_blank\" rel=\"noopener noreferrer\">YouTube</a></li>
                    <li><a href=\"https://www.tiktok.com/@fallow_restaurant?lang=en\" target=\"_blank\" rel=\"noopener noreferrer\">TikTok</a></li>
                </ul>
            </nav>
        </div>
        <div class=\"footer-logos\">
            <img src=\"images/awards/code-1.svg\" alt=\"Code 1 Award\">
            <img src=\"images/awards/hotdinners.svg\" alt=\"Hot Dinners Award\">
            <img src=\"images/awards/National-Restaurant-Awards.svg\" alt=\"National Restaurant Awards\">
            <img src=\"images/awards/Squaremeal.svg\" alt=\"Squaremeal Award\">
            <img src=\"images/awards/B-Corp-Logo-White-RGB.png\" alt=\"B Corp Certification\">
        </div>
    </div>
    <div class=\"footer-bottom\">
        <p>&copy; 2024 Lancaster's Restaurant. All Rights Reserved.</p>
    </div>
</footer>
";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "footer.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  42 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<footer role=\"contentinfo\">
    <div class=\"footer-container\">
        <div class=\"footer-section\">
            <h4>Opening Times</h4>
            <ul>
                <li>Mon – Fri: 07:30 am – 11 pm</li>
                <li>Sat: 9 am – 11 pm</li>
                <li>Sun: 11:30 am – 10 pm</li>
            </ul>
        </div>
        <div class=\"footer-section\">
            <h4>Address</h4>
            <address>
                52 Haymarket<br>
                London<br>
                SW1Y 4RP
            </address>
        </div>
        <div class=\"footer-logo\">
            <img src=\"images/logos/Lancaster's-logos_white_cropped.png\" alt=\"Lancaster's Restaurant Logo\">
        </div>
        <div class=\"footer-section\">
            <h4>Follow Us</h4>
            <nav aria-label=\"Social Media Links\">
                <ul class=\"social-links\">
                    <li><a href=\"https://www.instagram.com/fallowrestaurant\" target=\"_blank\" rel=\"noopener noreferrer\">Instagram</a></li>
                    <li><a href=\"https://www.youtube.com/channel/UCJ901NqoRaXMnIm7aOjLyuA\" target=\"_blank\" rel=\"noopener noreferrer\">YouTube</a></li>
                    <li><a href=\"https://www.tiktok.com/@fallow_restaurant?lang=en\" target=\"_blank\" rel=\"noopener noreferrer\">TikTok</a></li>
                </ul>
            </nav>
        </div>
        <div class=\"footer-logos\">
            <img src=\"images/awards/code-1.svg\" alt=\"Code 1 Award\">
            <img src=\"images/awards/hotdinners.svg\" alt=\"Hot Dinners Award\">
            <img src=\"images/awards/National-Restaurant-Awards.svg\" alt=\"National Restaurant Awards\">
            <img src=\"images/awards/Squaremeal.svg\" alt=\"Squaremeal Award\">
            <img src=\"images/awards/B-Corp-Logo-White-RGB.png\" alt=\"B Corp Certification\">
        </div>
    </div>
    <div class=\"footer-bottom\">
        <p>&copy; 2024 Lancaster's Restaurant. All Rights Reserved.</p>
    </div>
</footer>
", "footer.twig", "/var/www/html/website/templates/footer.twig");
    }
}
