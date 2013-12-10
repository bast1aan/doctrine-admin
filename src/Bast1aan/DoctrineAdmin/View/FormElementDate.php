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

namespace Bast1aan\DoctrineAdmin\View {
	use \DateTime;
	use \Bast1aan\DoctrineAdmin\Exception;
	class FormElementDate extends FormElementText {
		
		/**
		 * Define format
		 * @return string
		 */
		protected function getFormat() {
			return 'Y-m-d';
		}

		protected function getFieldType() {
			return 'date';
		}

		/**
		 * @return string
		 */
		public function getValue() {
			$property = $this->getProperty();
			$date = $property->getValue();
			if ($date instanceof DateTime) {
				return $date->format($this->getFormat());
			} else {
				throw new Exception(sprintf('Value of property of class %s is not a DateTime', get_class($property)));
			}
		}

		/**
		 * @param string
		 */
		public function setValue($value) {
			$proprety = $this->getProperty();
			$property->setValue(DateTime::createFromFormat($this->getFormat(), $value));
		}
	}
}
