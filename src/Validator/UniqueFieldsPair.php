<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
class UniqueFieldsPair extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    
    public $errorPath;
    public $fields = [];
    public $entityClass = null;
	
	public function getRequiredOptions()
	{
		return ['fields', 'entityClass', 'errorPath'];
	}

	/**
	* {@inheritdoc}
	*/
	public function getTargets()
	{  
        //get all properties of class
		return self::CLASS_CONSTRAINT;
	}

    public $message = 'Record in database with the same name and intensity already exist';
}
