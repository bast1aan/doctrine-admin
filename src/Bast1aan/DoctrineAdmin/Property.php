<?php

namespace Bast1aan\DoctrineAdmin {
	interface Property {
		
		function getValue();
		function getName();
		function __getString();
		function isNull();
	}
}