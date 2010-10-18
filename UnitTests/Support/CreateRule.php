<?php

namespace PHPAccessControl\UnitTests\Support;

class CreateRule
{
	static public function allow(
		\PHPAccessControl\Specification\Specification $specification
	) {
		return new \PHPAccessControl\Rule\SituationBasedRule($specification, true);
	}

	static public function deny(
		\PHPAccessControl\Specification\Specification $specification
	) {
		return new \PHPAccessControl\Rule\SituationBasedRule($specification, false);
	}
}