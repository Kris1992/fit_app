<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"CLASS", "ANNOTATION"})
 */

/*Target tells the annotation system where your annotation is allowed to be used.
What if you instead want your annotation to be put above a class? Open the UniqueEntity class as an example. Yep, you would use the CLASS target.*/

class UniqueUser extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */

    public $errorPath;
    public $fields = [];
	
	public function getRequiredOptions()
	{
		return ['fields'];
	}

	public function getDefaultOption()
	{
		return 'fields';
	}

	/**
	* {@inheritdoc}
	*/
	public function getTargets()
	{
		return self::CLASS_CONSTRAINT;
	}

     public $message = 'This e-mail address is already registered!';
}
