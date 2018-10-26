<?php

namespace CreativeServices\Workshop\Application;

use CreativeServices\Workshop\Environment\EnvironmentInterface;
use CreativeServices\Workshop\Template\File\TemplateDirectoryInterface;
use CreativeServices\Workshop\Template\TemplateInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Application implements ApplicationInterface
{
    /**
     * @var EnvironmentInterface
     */
    private $environment;

    public function __construct(EnvironmentInterface $environment)
    {
        $this->environment = $environment;
    }

    public function __invoke(Request $request = null)
    {
        $this->run($request);
    }

    public function respond(Request $request)
    {
        $path = $request->getPathInfo();
        if ($path === '/') {
            return new Response($this->makeIndex());
        }
        if ($path === '/favicon.ico') {
            return new BinaryFileResponse(__DIR__ . '/../../app/favicon.ico');
        }
        if ($this->isTemplatePath($path)) {
            return new Response($this->renderTemplate($path));
        }
        if ($this->isStaticAliasPath($path)) {
            $file = $this->getStaticFile($path);
            $headers = [];
            switch ($file->getExtension()) {
                case 'css':
                    $headers['Content-Type'] = 'text/css';
                    break;
                case 'js':
                    $headers['Content-Type'] = 'application/javascript';
                    break;
            }
            return new BinaryFileResponse($file, 200, $headers);
        }
        return new Response('Not found', 404);
    }

    public function run(Request $request = null)
    {
        if (!isset($request)) {
            $request = Request::createFromGlobals();
        }
        $response = $this->respond($request);
        $response->prepare($request);
        $response->send();
    }

    /**
     * @param string $path
     * @return \SplFileInfo
     */
    private function getStaticFile($path)
    {
        return $this->environment->getAsset($path);
    }

    private function getTemplates()
    {
        return $this->environment->getTemplates();
    }

    private function getTemplatesSortedByName()
    {
        $cmp = function (TemplateInterface $a, TemplateInterface $b) {
            return strnatcasecmp($a->getName(), $b->getName());
        };
        $templates = iterator_to_array($this->getTemplates(), false);
        usort($templates, $cmp);
        return $templates;
    }

    /**
     * @param string $path
     * @return boolean
     */
    private function isStaticAliasPath($path)
    {
        return $this->environment->hasAsset($path);
    }

    /**
     * @param string $path
     * @return boolean
     */
    private function isTemplatePath($path)
    {
        $templatePath = $this->makeTemplatePathFromRequestPath($path);
        return $this->getTemplates()->hasTemplate($templatePath);
    }

    /**
     * @return string
     * @throws \Twig_Error
     */
    private function makeIndex()
    {
        $context = ['templates' => $this->getTemplatesSortedByName()];
        $templates = $this->getTemplates();
        if ($templates instanceof TemplateDirectoryInterface) {
            $context['directory'] = $templates->getPath()->string();
        }
        return $this->makeTwig()->render('index.html.twig', $context);
    }

    /**
     * @param string $requestPath
     * @return string
     */
    private function makeTemplatePathFromRequestPath($requestPath)
    {
        return ltrim($requestPath, '/');
    }

    private function makeTwig()
    {
        return new \Twig_Environment(new \Twig_Loader_Filesystem(__DIR__ . '/../../templates'));
    }

    /**
     * @param string $path
     * @return string
     */
    private function renderTemplate($path)
    {
        /** @var TemplateInterface $template */
        $template = $this->getTemplates()->getTemplate($this->makeTemplatePathFromRequestPath($path));
        return $this->environment->render($template->getName());
    }
}