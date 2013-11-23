<?php

namespace Bast1aan\DoctrineAdmin {
	class AssociationProperty extends AbstractProperty {
		public function __toString() {
			try {
			$entity = new Entity($this->getValue(), $this->doctrineAdmin);
			} catch (\Exception $e) {
				return $e->getMessage();
			}
			return (string) $entity;
		}
	}
}