<?php
declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */
class UniqueRangeProperties extends Constraint
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
		return ['fields', 'errorPath', 'entityClass'];
	}

	/**
	* {@inheritdoc}
	*/
	public function getTargets()
	{
		return self::CLASS_CONSTRAINT;
	}

    public $message = 'The activity with the same name and speed range already exist';
}