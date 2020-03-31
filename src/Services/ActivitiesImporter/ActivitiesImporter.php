<?php

namespace App\Services\ActivitiesImporter;

use League\Flysystem\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Psr\Log\LoggerInterface;
use App\Services\FilesManager\FilesManagerInterface;
use App\Services\FileReader\FileReaderInterface;

use App\Services\Transformer\Activity\ActivityTransformer;
use App\Services\Factory\Activity\ActivityFactory;
use App\Services\ModelValidator\ModelValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;

//second way curl by symfony component
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;

class ActivitiesImporter implements ActivitiesImporterInterface
{

    private $logger;
    private $filesManagerInterface;
    private $uploadsDirectory;
    private $csvFileReader;
    private $modelValidatorInterface;
    private $em;


    /**
     * ActivitiesImporter Constructor
     * 
     * @param LoggerInterface $logger
     * @param FilesManagerInterface $filesManagerInterface
     */
    public function __construct(LoggerInterface $logger, FilesManagerInterface $filesManagerInterface, FileReaderInterface $csvFileReader, ModelValidatorInterface $modelValidatorInterface, EntityManagerInterface $em, string $uploadsDirectory)  
    {
        $this->logger = $logger;
        $this->filesManagerInterface = $filesManagerInterface;
        $this->uploadsDirectory = $uploadsDirectory;
        $this->csvFileReader = $csvFileReader;
        $this->modelValidatorInterface = $modelValidatorInterface;
        $this->em = $em;
    }

    public function import(File $file): array
    {

        try {
            $filename = $this->filesManagerInterface->upload($file, 'activity_csv');
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }

        $absoluteFilePath = $this->uploadsDirectory.'/activity_csv/'.$filename;

        //To do something new I decide to validate csv file by using CSVLint api and curl
        //$isValidCSV = $this->curlCSVValidator($absoluteFilePath);
        $isValidCSV = $this->curlCSVValidator2($absoluteFilePath);
        
        if($isValidCSV) {
            $this->csvFileReader->read($absoluteFilePath);
            $activitiesArray = $this->csvFileReader->parseToArray();
            $activities = [];

            $result['valid'] = 0;
            $result['invalid'] = 0;
            $result['invalidRows'] = [];

            foreach ($activitiesArray as $key => $activityArray) {
                try {
                    $activityTransformer = ActivityTransformer::chooseTransformer($activityArray['type']);
                    $activityModel = $activityTransformer->transformArrayToModel($activityArray);
                    
                    //Validation Model data
                    $isValid = $this->modelValidatorInterface->isValid($activityModel);
                    if($isValid) {
                        $activityFactory = ActivityFactory::chooseFactory($activityModel->getType());
                        //$activities[$key] = $activityFactory->create($activityModel);
                        $activity = $activityFactory->create($activityModel);
                        //in inheritance joined and single table (https://github.com/doctrine/orm/issues/6248) is impossible to make uniqueConstraints on few columns so it must flush one by one or in future i make for this array validator
                        $this->em->persist($activity);
                        $this->em->flush();

                        $result['valid']++;    
                    } else {
                        $result['invalid']++;
                        $violations = $this->modelValidatorInterface->getErrors();

                        //Take just first violation
                        $violation = $violations[0]->getMessage();
                        array_push(
                            $result['invalidRows'], 
                            [ 
                                'id' => $key+2,//header + num from 1 not 0
                                'message'=> $violation,
                            ]
                        );
                    }
                } catch (\Exception $e) {
                    $result['invalid']++;

                    $violation = $e->getMessage();
                        array_push(
                            $result['invalidRows'], 
                            [ 
                                'id' => $key+2,//header + num from 1 not 0
                                'message'=> $violation,
                            ]
                        );
                }
            }    
            
        }

        $this->filesManagerInterface->delete($filename, 'activity_csv');
        
        if (!$isValidCSV) {
            throw new \Exception("Uploaded CSV file is not valid");
        }

        return $result;
    }

    private function curlCSVValidator(string $absoluteFilePath): bool
    {
        //curl -F --data "files[]=filePath" http://csvlint.io/package.json

        $curl = curl_init();
        $url = 'http://csvlint.io/package';
        curl_setopt($curl, CURLOPT_URL, $url);
        //curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");   
        curl_setopt($curl, CURLOPT_HTTPHEADER,array('Content-Type: multipart/form-data'));
        curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);   
        curl_setopt($curl, CURLOPT_FORBID_REUSE, true);  
        curl_setopt($curl, CURLOPT_TIMEOUT, 100);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        $curlFile = new \CURLFile($absoluteFilePath);
        $postArray = [
            'files[]' => $curlFile
        ];
        
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postArray);

        $result = curl_exec($curl);

        curl_close($curl);

        if(preg_match('!Your CSV is valid!', $result)) {
            return true;
        }

        return false;
        //throw new Exception("CSV is not valid or site CSVLint is disconnect");
        //logger
    }

    // second way curl (just for fun)
    private function curlCSVValidator2(string $absoluteFilePath): bool
    {

        $client = new CurlHttpClient();
        $formField = [
            'files[]' => DataPart::fromPath($absoluteFilePath),
        ];
        $formData = new FormDataPart($formField);
        $response = $client->request('POST', 'http://csvlint.io/package', [
            'headers' => $formData->getPreparedHeaders()->toArray(),
            'body' => $formData->bodyToIterable(),
        ]);

        $result = $response->getContent();

        if(preg_match('!Your CSV is valid!', $result)) {
            return true;
        }

        return false;
    }
}



