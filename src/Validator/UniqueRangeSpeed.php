<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
class UniqueRangeSpeed extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */

    public $errorPath;
    public $fields = [];
	
    //rebuild
	public function getRequiredOptions()
	{
		return ['fields', 'errorPath'];
	}

	/**
	* {@inheritdoc}
	*/
	public function getTargets()
	{
		return self::CLASS_CONSTRAINT;
	}

    //rebuild it to be more universal
    public $message = 'The activity with the same name and speed range already exist';
}