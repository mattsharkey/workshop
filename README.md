
$templates = new TemplateDirectory('/path/to/workshop/templates');
$environment = new Environment($twig);
print $environment->render($templates->get('path/to/template.twig');

$server = new Server($environment, $templates);
$server->addAlias('/assets', '/path/to/static/assets');
$server->respond();
