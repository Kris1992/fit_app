<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Form\Model\Workout\WorkoutSpecificFormModel;
use App\Form\WorkoutWithMapFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Services\FileDecoder\FileDecoderInterface;

use Symfony\Component\Form\FormInterface;

class RouteWorkoutController extends AbstractController
{

     /**
     * @Route("route/workout/draw_route", name="route_workout_draw_route", methods={"POST", "GET"})
     * @IsGranted("ROLE_USER")
     */
    public function drawRoute(string $map_api_key)
    {
        $form = $this->createForm(WorkoutWithMapFormType::class);

        return $this->render('route_workout/add_drawed.html.twig', [
            'map_api_key' => $map_api_key,
            'workoutForm' => $form->createView()
        ]);
    }

    /**
     * @Route("api/route/workout/add_drawed", name="api_route_workout_add_drawed")
     */
    public function addDrawedAction(Request $request, FileDecoderInterface $base64Decoder)
    {
        $data = json_decode($request->getContent(), true);

        if($data === null) {
            throw new BadRequestHttpException('Invalid Json');    
        }

        $form = $this->createForm(WorkoutWithMapFormType::class);
        $form->submit($data['formData']);

        if (!$form->isValid()) {
            $errors = $this->getErrorsFromForm($form);

            return $this->json(
            $errors,
            400
            );
        }

        $workoutSpecificModel = $workoutSpecificExtender->fillWorkoutModel($workoutSpecificModel, $this->getUser());

        if ($workoutSpecificModel) {

        }

        //$workoutSpecificModel = $form->getData();
        //In future create for each user unique login
        //$imageDestination = '/workouts_images/'.$user->getSecondName().'/';
        //$filePath = $base64Decoder->decode($data['image'], $imageDestination);
        
    


        //return $this->render('route_workout/addDrawed.html.twig', [
            
        //]);
    }

    

    protected function getErrorsFromForm(FormInterface $form)
    {
        foreach ($form->getErrors() as $error) {
            return $error->getMessage();
        }

        $errors = array();
        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childError = $this->getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childError;
                }
            }
        }

        return $errors;
    }
}
