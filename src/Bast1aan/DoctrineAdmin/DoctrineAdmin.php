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
	
	use Doctrine\ORM\EntityManager;
	use Doctrine\Common\Collections\ArrayCollection;
	
	class DoctrineAdmin {
		
		/**
		 * @var EntityManager
		 */
		private $em;
		
		/**
		 * @var string
		 */
		private $entityName;
		
		public function __construct(EntityManager $em) {
			$this->em = $em;
			
			
			
			
			/*
			$meta = $em->getClassMetadata($entityName);
			var_dump($meta->fieldMappings);
			var_dump($meta->associationMappings);
			//$meta->reflClass->
			 * 
			 */
		}
		
		/**
		 * @param string $entityName
		 * @return Collection|Entity[]
		 */
		public function getCollection($entityName) {
			$repository = $this->em->getRepository($entityName);
			$entities = new ArrayCollection($repository->findAll());
			
			return new Collection($entities, $entityName, $this);
		}
		
		/**
		 * 
		 * @return EntityManager
		 */
		public function getEntityManager() {
			return $this->em;
		}
	}
}
