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
		 * @var Config
		 */
		private $config;
		
		/**
		 * @var string
		 */
		private $entityName;
		
		public function __construct(EntityManager $em, Config $config = null) {
			$this->em = $em;
			$this->config = $config;
			
			
			
			/*
			$meta = $em->getClassMetadata($entityName);
			var_dump($meta->fieldMappings);
			var_dump($meta->associationMappings);
			//$meta->reflClass->
			 * 
			 */
		}
		
		/**
		 * @return Config
		 */
		public function getConfig() {
			return $this->config;
		}

		/**
		 * @param Config $config
		 */
		public function setConfig(Config $config) {
			$this->config = $config;
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
		
		/**
		 * Find an entity by entity name and entity ID. entity ID is a
		 * string as provided from Entity::getIdAsStr()
		 * 
		 * @param string $entityName
		 * @param string $entityId
		 * @return Entity
		 * @throws Exception
		 */
		public function find($entityName, $entityId) {
			$classMetaData = $this->em->getClassMetadata($entityName);
			
			$idNames = $classMetaData->getIdentifierFieldNames();
			
			$idValues = explode('-', $entityId);
			
			if (count($idNames) != count($idValues)) {
				throw new Exception('Primary key values don\'t match amount of primary key fields');
			}
			
			$id = array();
			for($i = 0; $i < count($idNames); ++$i)
				$id[$idNames[$i]] = str_replace('--', '-', $idValues[$i]);
			
			$entityObj = $this->em->find($entityName, $id);
			if ($entityObj != null)
				return Entity::factory($entityObj, $this);
		}
	}
}
