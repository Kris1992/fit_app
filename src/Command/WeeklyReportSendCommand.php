<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Repository\UserRepository;
use App\Repository\WorkoutRepository;
use App\Services\Mailer\MailingSystemInterface;

class WeeklyReportSendCommand extends Command
{
    protected static $defaultName = 'app:weekly-report:send';
    private $userRepository;
    private $wokoutRepository;
    private $mailer;

    public function __construct(UserRepository $userRepository,WorkoutRepository $wokoutRepository, MailingSystemInterface $mailer)
    {
        parent::__construct(null);
        $this->userRepository = $userRepository;
        $this->wokoutRepository = $wokoutRepository;
        $this->mailer = $mailer;
    }

    protected function configure()
    {
        $this
            ->setDescription('Send weekly wokouts report to users')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $users = $this->userRepository
            ->findAll();
        $io->progressStart(count($users));
        foreach ($users as $user) {
            $io->progressAdvance();

            $workouts = $this->wokoutRepository
                ->findWorkoutsFromLastWeek($user);

            if (count($workouts) === 0) {
                continue;
            }

            $this->mailer->sendWeeklyReportMessage($user, $workouts);
        }
        $io->progressFinish();
        $io->success('Weekly reports were sent to users!');

        return 0;
    }
}
