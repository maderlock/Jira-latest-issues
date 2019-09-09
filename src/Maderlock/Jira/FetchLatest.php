<?php

namespace Maderlock\Jira;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\JiraException;

class FetchLatest extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'jira:fetch-latest';

    protected function configure()
    {
        // ...
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $jql = 'status in (open) ORDER BY rank';

	try {
	    $issueService = new IssueService();

            $ret = $issueService->search($jql);

            // Store per project
            $issuesPerProj = [];
            foreach ($ret->getIssues() as $issue) {
                if (!isset($issuesPerProj[$issue->fields->project->key])) {
                    $issuesPerProj[$issue->fields->project->key] = $issue;
                }
            }
            foreach ($issuesPerProj as $projKey => $issue) {
                $output->writeln("{$issue->key}: {$issue->fields->summary}");
            }    
        } catch (JiraException $e) {
            $output->writeln($e->getMessage());
        }
    }
}
