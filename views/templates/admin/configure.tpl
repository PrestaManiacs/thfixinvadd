{*
* 2007-2022 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2022 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="panel">
	<h3><i class="icon icon-info"></i> {l s='Module Info' mod='thfixinvaddr'}</h3>
	<p>{l s='The "Invalid Address" error occurs when the address assigned to a shopping cart has been deleted from the database.' mod='thfixinvaddr'}</p>

	<span>{l s='The module checks which shopping carts have this problem, and can solve it in 2 ways:' mod='thfixinvaddr'}</span>
	<ul>
		<li><strong>{l s='Remove Cart Items from DB' mod='thfixinvaddr'}</strong> - {l s='Remove shopping carts with invalid addresses from the ps_cart table' mod='thfixinvaddr'}</li>
		<li><strong>{l s='Set ID Address to 0' mod='thfixinvaddr'}</strong> - {l s='Set ID 0 for invalid addresses' mod='thfixinvaddr'}</li>
	</ul>

	<span>{l s='To avoid this problem in the future, this fix can be implemented:' mod='thfixinvaddr'}</span>
	<a href="https://github.com/PrestaShop/PrestaShop/pull/11416">https://github.com/PrestaShop/PrestaShop/pull/11416</a>
</div>
