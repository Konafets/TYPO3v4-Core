<?php
namespace TYPO3\CMS\Core\Tests\Unit\Type\Fixture\Enumeration;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Sascha Egerer <sascha.egerer@dkd.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * This is an complete enumeration with all possible constant values
 */
class CompleteEnumeration extends \TYPO3\CMS\Core\Type\Enumeration {
	const __default = self::INTEGER_VALUE;
	const INTEGER_VALUE = 1;
	const STRING_VALUE = 'foo';
}