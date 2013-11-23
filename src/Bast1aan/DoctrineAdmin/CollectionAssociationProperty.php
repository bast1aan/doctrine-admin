<?php
/*
 * Doctrine Admin
 * Copyright (C) 2013 Bastiaan Welmers, bastiaan@welmers.net
 * 
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 * 
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 */

namespace Bast1aan\DoctrineAdmin {
	use Countable;
	use Iterator;
	use Doctrine\Common\Collections\Collection as DoctrineCollection;
	
	class CollectionAssociationProperty extends AbstractProperty implements Iterator, Countable {
		
		/**
		 * @var array
		 */
		private $value;
		
		/**
		 *
		 * @var int
		 */
		private $i;
		
		public function __construct($name, $value, $type, DoctrineAdmin $doctrineAdmin) {
			parent::__construct($name, $value, $type, $doctrineAdmin);
			if (is_array($value)) {
				$this->value = $value;
			} elseif ($value instanceof DoctrineCollection) {
				$this->value = $value->toArray();
			} else {
				return new Exception('Collection property value not an instance of Doctrine\Common\Collections\Collection and not an array');
			}
		}
		
		public function count() {
			count($this->value);
		}
		
		public function current() {
			return new Entity($this->value[$this->i], $this->doctrineAdmin);
		}

		public function key() {
			return $this->i;
		}

		public function next() {
			++$this->i;
		}

		public function rewind() {
			$this->i = 0;
		}

		public function valid() {
			return isset($this->value[$this->i]);
		}

	
	}
}