<?php

/**

 *
 * @package    多语言类包

 * @filesource
 */

namespace system\lib;

/**
 * Handle system messages and localization.
 *
 * Locale-based, built on top of PHP internationalization.
 *
 * @package lib
 */
class Language
{

	/**
	 * Stores the retrieved language lines
	 * from files for faster retrieval on
	 * second use.
	 *
	 * @var array
	 */
	protected $language = [];

	/**
	 * The current language/locale to work with.
	 *
	 * @var string
	 */
	protected $locale;



	/**
	 * Stores filenames that have been
	 * loaded so that we don't load them again.
	 *
	 * @var array
	 */
	protected $loadedFiles = [];

	//--------------------------------------------------------------------

	public function __construct()
	{
		$this->loadedLanguageFiles();
	}
	/**
	 * 将语言文件的数组包含进来
	 */
	public function loadedLanguageFiles()
	{
		$allLangDir = [SYSTEMPATH . "Language", APPPATH . "Language"];
		foreach ($allLangDir as $languagePath) {
			if (is_dir($languagePath)) {
				$dir = scandir($languagePath);
				foreach ($dir as $value) {
					$subPath = $languagePath . '/' . $value;
					if ($value == '.' || $value == '..') {
						continue;
					} else if (is_dir($subPath)) {
						//子目录
						if(!isset($this->language[$value])){
							$this->language[$value] = [];
						}
						
						$subdir = scandir($subPath); //继续扫描每个语言文件夹下的文件
						foreach ($subdir as $subPathFile) {
							$langfile = $subPath . "/" . $subPathFile;
							if ($subPathFile == '.' || $subPathFile == '..') {
								continue;
							} else if (is_file($langfile)) {
								$langFileVal = include_once($langfile);
								$newLangArray=array_merge($this->language[$value], $langFileVal);
							
								$this->language[$value] = $newLangArray;
							}
						}
					} else {
						//.$path 可以省略，直接输出文件名
						continue;
					}
				}
			}
		}
	}
	/**
	 * 输出语言
	 */
	public function lang($key = "", $locale = "")
	{
		$config = BaseService::config();
		$locale = (isset($locale) && $locale != "") ? $locale : $config->actionLang;
		//echo "当前语言". $locale.$key;
		$word = '';
		if (isset($key) && $key != "") {
			
			if (isset($this->language[$locale]) && isset($this->language[$locale][$key])) {
				
				return $this->language[$locale][$key];
			}
		}
		return $word;
	}
}
