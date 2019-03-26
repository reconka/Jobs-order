<?php

namespace App\Commands;

use App\Models\JobManager;
use LaravelZero\Framework\Commands\Command;

class JobProcessing extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'JobProcessing';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Generate a List of the Jobs';

    /**
     * Create a interactive dialog
     * @return array
     */
    private function showQuestionDialog(): array
    {
        $i = 0;
        $exit = false;
        $resultArray = [];

        do {
            $i++;
            $elementName = $this->ask("Please enter {$i}. element:");
            $dependency = $this->ask("Please enter {$i}. dependency:");
            if (!$this->confirm('Do you wish to continue?')) {
                $exit = true;
            }
            array_push(
                $resultArray,
                [
                    'elementName' => $elementName,
                    'dependency' => $dependency
                ]
            );
        } while (!$exit);

        return $resultArray;
    }

    /**
     * Handle the CLI request
     */
    public function handle()
    {
        $this->info('Welcome to Job processor!
        Imagine we have a list of jobs, each represented by a character.
        Because certain jobs must be done before others, a job may have a dependency on another job.');

        $arr = $this->showQuestionDialog();
        $getStringShortResult = new JobManager($arr);

        try {
            $result = $getStringShortResult->getList();
            $this->info('Your result:' . json_encode($result));
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}
