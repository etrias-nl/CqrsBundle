<?php

declare(strict_types=1);


namespace Etrias\CqrsBundle\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class InvalidateTagsCommand extends Command
{
    /**
     * @var TagAwareAdapterInterface
     */
    protected $cache;

    public function __construct($name = null,  TagAwareAdapterInterface $cache)
    {
        parent::__construct($name);

        $this->cache = $cache;
    }


    protected function configure()
    {
        $this
            ->setName('cqrs:invalidate:tags')
            ->addArgument(
                'tags',
                InputArgument::IS_ARRAY | InputArgument::REQUIRED,
                'tags to invalidate'
            )
            ->setDescription('Invalidate tags (separate multiple names with a space)')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $tags = $input->getArgument('tags');

        $this->cache->invalidateTags($tags);

        $io->success('tags invalidated');
    }
}
