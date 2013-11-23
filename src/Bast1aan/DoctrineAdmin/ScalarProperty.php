<?php

namespace Bast1aan\DoctrineAdmin {
	use DateTime;
	class ScalarProperty extends AbstractProperty {
		public function __toString() {
			$value = $this->getValue();
			if ($value instanceof DateTime) {
				return $value->format('Y-m-d');
			} elseif ($value === null) {
				return 'NULL';
			} else {
				return (string) $value;
			}
		}
	}
}