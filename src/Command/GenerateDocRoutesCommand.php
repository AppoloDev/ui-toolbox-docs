<?php

namespace AppoloDev\UIToolboxDocBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

#[AsCommand(
    name: 'ui-toolbox:generate-doc-routes',
    description: 'Génère les routes de la documentation à partir des fichiers Twig présents dans docs/templates.',
)]
class GenerateDocRoutesCommand extends Command
{
    private const DOC_TEMPLATE_PATH = __DIR__.'/../../../docs/templates';
    private const ROUTES_OUTPUT_PATH = __DIR__.'/../../../docs/config/routes/documentation.yaml';

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $filesystem = new Filesystem();

        if (!$filesystem->exists(self::DOC_TEMPLATE_PATH)) {
            $output->writeln('<error>Répertoire des templates de documentation non trouvé : '.self::DOC_TEMPLATE_PATH.'</error>');

            return Command::FAILURE;
        }

        $finder = new Finder();
        $finder->files()->in(self::DOC_TEMPLATE_PATH)->name('*.html.twig');

        $routes = [
            'ui_toolbox_dashboard' => [
                'path' => '/',
                'controller' => 'AppoloDev\\UIToolboxBundleDocs\\Controller\\Dashboard',
                'methods' => ['GET'],
            ],
        ];

        foreach ($finder as $file) {
            $relativePath = $file->getRelativePathname(); // e.g. actions/button.html.twig
            $parts = \explode(\DIRECTORY_SEPARATOR, $relativePath);
            $filename = \pathinfo(\array_pop($parts), \PATHINFO_FILENAME); // button
            $category = \implode('/', $parts); // actions

            $routeName = 'ui_toolbox_'.\strtolower($filename);
            $path = '/'.\strtolower($category).'/'.\strtolower($filename);
            $controller = 'AppoloDev\\UIToolboxBundle\\Docs\\Controller\\'.
                \str_replace('/', '\\', \ucfirst($category)).'\\'.\ucfirst($filename);

            $routes[$routeName] = [
                'path' => $path,
                'controller' => $controller,
                'methods' => ['GET'],
            ];
        }

        $yaml = '';
        foreach ($routes as $name => $data) {
            $yaml .= $name.":\n";
            $yaml .= '    path: '.$data['path']."\n";
            $yaml .= '    controller: '.$data['controller']."\n";
            $yaml .= '    methods: ['.\implode(', ', $data['methods'])."]\n\n";
        }

        $filesystem->dumpFile(self::ROUTES_OUTPUT_PATH, $yaml);
        $output->writeln('<info>Fichier de routes généré : '.self::ROUTES_OUTPUT_PATH.'</info>');

        return Command::SUCCESS;
    }
}
