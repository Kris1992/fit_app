<?php
//do usunięcia było tymczasowo

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Entity\Workout;
use App\Entity\Activity;

class WorkoutUtilityController extends AbstractController
{

    protected function calculateBurnoutEnergy(Workout $workout)
    {
    	$activity = $workout->getActivity();
		$activityEnergy =  $activity->getEnergy();

		$workoutDuration = $workout->getDurationSeconds();
		$burnoutEnergy = $activityEnergy * ($workoutDuration/(60*60));

     	return $burnoutEnergy;
    }
}
