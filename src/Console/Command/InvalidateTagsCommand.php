<?php

declare(strict_types=1);

namespace Etrias\CqrsBundle\Console\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'cqrs:invalidate:tags')]
class InvalidateTagsCommand extends Command
{
    protected TagAwareAdapterInterface $cache;

    public function __construct(TagAwareAdapterInterface $cache)
    {
        parent::__construct();

        $this->cache = $cache;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('tags', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'tags to invalidate')
            ->setDescription('Invalidate tags (separate multiple names with a space)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $tags = $input->getArgument('tags');

        $this->cache->invalidateTags($tags);

        $io->success('tags invalidated');

        return 0;
    }
}
