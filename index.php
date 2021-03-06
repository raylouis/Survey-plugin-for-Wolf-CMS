<?php

/* Security measure */
if (!defined('IN_CMS')) exit();

/**
 * The Survey Plugin for Wolf CMS makes it easy to conduct custom surveys.
 *
 * @author Robert Hallsey <rhallsey@yahoo.com>
 * @copyright Robert Hallsey, 2015
 * @license http://www.gnu.org/licenses/gpl.html GPLv3 license
 *
 * This file is part of the Survey Plugin for Wolf CMS.
 *
 * The Survey Plugin for Wolf CMS is free software: you can redistribute
 * it and/or modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * The Survey Plugin for Wolf CMS is distributed in the hope that it
 * will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See
 * the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

Plugin::setInfos(array(
	'id'          => 'survey',
	'title'       => __('Survey'),
	'description' => __('Conduct surveys in WolfCMS.'),
	'version'     => '1.0',
	'license'     => 'GPLv3',
	'author'      => 'Robert Hallsey',
	'website'     => 'http://www.clicketyhome.com/',
	//'update_url'  => 'http://www.wolfcms.org/plugin-versions.xml',
	'require_wolf_version' => '0.7.7'
));

// Survey plugin's home
define('SURVEY_BASE_PATH', PLUGINS_ROOT . DS . 'survey' . DS);

define('SURVEY_RESPONSE_FILE_EXT', 'csv');
define('SURVEY_ICONS', URL_PUBLIC . 'wolf/plugins/survey/icons/');

// Add the plugin's tab and controller
Plugin::addController('survey', __('Survey'));

// Add the models to the autoLoader
AutoLoader::addFile('Survey', CORE_ROOT . '/plugins/survey/Survey.php');

function survey_conduct($given_survey) {
	if ($_SERVER['REQUEST_METHOD'] == 'GET') {
		$survey = new Survey(public_file($given_survey));
		$survey->prepareSurvey();
		Flash::set('survey_running', true);
	}
	else { // must be POST
		if (!Flash::get('survey_running')) exit ('No running survey');
		$survey = new Survey($_POST['survey_file']);
		if (!$survey->processSurvey($_POST['survey_save'], $_POST['survey_data'])) {
			Flash::set('survey_running', true);
		}
	}
	echo $survey->theForm();
}

function survey_summarize($given_survey) {
	$survey = new Survey(public_file($given_survey));
	$survey->prepareSummary();
	echo $survey->theSummary();
}

function public_file ($file){
	if (file_exists('public/' . $file)) {
		$file = 'public/' . $file;
	}
	return $file;
}