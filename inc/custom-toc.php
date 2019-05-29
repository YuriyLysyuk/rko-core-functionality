<?php
/**
 * Содержание (оглавление) для больших постов.
 *
 * @package      CoreFunctionality
 * @author       Yuriy Lysyuk
 * @since        1.0.0
 * @license      GPL-2.0+
**/

## Вывод содержания вверху после указанного параграфа, автоматом для всех записей
	add_filter( 'the_content', 'contents_at_top_after_nsep', 20 );
	function contents_at_top_after_nsep( $text ) {
		if( ! is_singular() )
			return $text;

	// параметры оглавления
		$args = array(
			'min_length' => 3000,
			'title'      => 'Содержание',
			'shortcode'  => 'ly_toc',
			'css'        => false,
			'markup'     => true,
			'selectors'  => array('h2','h3','h4'),
			'to_menu'    => '',
		);

	// настройки разделителя
	$_sep = '</p>'; // разделитель в тексте
	$_sep_num = 1;  // после какого по порядку разделителя вставлять оглавление?

	// погнали...
	$ex_text = explode( $_sep, $text, $_sep_num + 1 );

	// если подходящий по порядку разделитель найден в тексте
	if( isset($ex_text[$_sep_num]) ){
		$contents = Kama_Contents::init( $args )->make_contents( $ex_text[$_sep_num] );
		// добавляем рекламный блок до или после содержания
		if (function_exists ('adinserter')) {
			$ads_before_toc = adinserter (1);
			$ads_after_toc = adinserter (2);
			$contents = $ads_before_toc . $contents . $ads_after_toc;
		}
		$ex_text[$_sep_num] = $contents . $ex_text[$_sep_num];

		$text = implode( $_sep, $ex_text );
	}
	// просто в верху текста
	else {
		$contents = Kama_Contents::init( $args )->make_contents( $text );

		$text = $contents . $text;
	}
	
	return $text;
}

/**
 * Содержание (оглавление) для больших постов.
 *
 * Author: Kama
 * Page: http://wp-kama.ru/?p=1513
 * ver: 3.14
 *
 * Changelog: http://wp-kama.ru/?p=1513#obnovleniya
 */
class Kama_Contents {

		// defaults options
	public $opt = array(
				// Отступ слева у подразделов в px.
		'margin'     => 40,
				// Теги по умолчанию по котором будет строиться оглавление. Порядок имеет значение.
				// Кроме тегов, можно указать атрибут classа: array('h2','.class_name'). Можно указать строкой: 'h2 h3 .class_name'
		'selectors'  => array('h2','h3','h4'),
				// Ссылка на возврат к оглавлению. '' - убрать ссылку
		'to_menu'    => 'к содержанию ↑',
				// Заголовок. '' - убрать заголовок
		'title'      => 'Содержание:',
				// Css стили. '' - убрать стили
		'css'        => '.kc__gotop{ display:block; text-align:right; }
		.kc__title{ font-style:italic; padding:1em 0; }
		.kc__anchlink{ color:#ddd!important; position:absolute; margin-left:-1em; }',
				// JS код (добавляется после HTML кода)
		'js'  => '',
				// Минимальное количество найденных тегов, чтобы оглавление выводилось.
		'min_found'  => 2,
				// Минимальная длина (символов) текста, чтобы оглавление выводилось.
		'min_length' => 2000,
				// Ссылка на страницу для которой собирается оглавление. Если оглавление выводиться на другой странице...
		'page_url'   => '',
				// Название шоткода
		'shortcode'  => 'contents',
				// Оставлять символы в анкорах
		'spec'       => '\'.+$*~=',
				// Какой тип анкора использовать: 'a' - <a name="anchor"></a> или 'id' -
		'anchor_type' => 'id',
				// Включить микроразметку?
		'markup'      => false,
				// Добавить 'знак' перед подзаголовком статьи со ссылкой на текущий анкор заголовка. Укажите '#', '&' или что вам нравится :)
		'anchor_link' => '',
				// минимальное количество символов между заголовками содержания, для которых нужно выводить ссылку "к содержанию".
				// Не имеет смысла, если параметр 'to_menu' отключен. С целью производительности, кириллица считается без учета кодировки.
				// Поэтому 800 символов кириллицы - это примерно 1600 символов в этом параметре. 800 - расчет для сайтов на кириллице...
		'tomenu_simcount' => 800,
	);

		public $contents; // collect html contents

		private $temp;

		static $inst;

		function __construct( $args = array() ){
			$this->set_opt( $args );
			return $this;
		}

		/**
		 * Create instance
		 * @param  array [$args = array()] Options
		 * @return object Instance
		 */
		static function init( $args = array() ){
			is_null( self::$inst ) && self::$inst = new self( $args );
			if( $args ) self::$inst->set_opt( $args );
				return self::$inst;
			}

			function set_opt( $args = array() ){
				$this->opt = (object) array_merge( (array) $this->opt, (array) $args );
			}

		/**
		 * Обрабатывает текст, превращает шоткод в нем в оглавление.
		 * @param (string) $content текст, в котором есть шоткод.
		 * @param (string) $contents_cb callback функция, которая обработает список оглавления.
		 * @return Обработанный текст с оглавлением, если в нем есть шоткод.
		 */
		function shortcode( $content, $contents_cb = '' ){
			if( false === strpos( $content, '['. $this->opt->shortcode ) )
				return $content;

				// get contents data
			if( ! preg_match('~^(.*)\['. $this->opt->shortcode .'([^\]]*)\](.*)$~s', $content, $m ) )
				return $content;

			$contents = $this->make_contents( $m[3], $m[2] );

			if( $contents && $contents_cb && is_callable($contents_cb) )
				$contents = $contents_cb( $contents );

			return $m[1] . $contents . $m[3];
		}

		/**
		 * Заменяет заголовки в переданном тексте (по ссылке), создает и возвращает оглавление.
		 * @param (string)        $content текст на основе которого нужно создать оглавление.
		 * @param (array/string)  $tags    массив тегов, которые искать в переданном тексте.
		 *                                 Можно указать: имена тегов "h2 h3" или классы элементов ".foo .foo2".
		 *                                 Если в теги добавить маркер "embed" то вернется только тег <ul>
		 *                                 без заголовка и оборачивающего блока. Нужно для использования внутри текста, как список.
		 * @return                html код оглавления.
		 */
		function make_contents( & $content, $tags = '' ){
				// return if text is too short
			if( mb_strlen( strip_tags($content) ) < $this->opt->min_length )
				return;

			$this->temp     = $this->opt;
			$this->contents = array();

			if( ! $tags )
				$tags = $this->opt->selectors;

			if( is_string($tags) )
				$tags = array_map('trim', preg_split('/[ ,]+/', $tags ) );

				$tags = array_filter($tags); // del empty

				// check tags
				foreach( $tags as $k => $tag ){
						// remove special marker tags and set $args
					if( in_array( $tag, array('embed','no_to_menu') ) ){
						if( $tag == 'embed' ) $this->temp->embed = true;
						if( $tag == 'no_to_menu' ) $this->opt->to_menu = false;

						unset( $tags[ $k ] );
						continue;
					}

						// remove tag if it's not exists in content
					$patt = ( ($tag[0] == '.') ? 'class=[\'"][^\'"]*'. substr($tag, 1) : "<$tag" );
					if( ! preg_match("/$patt/i", $content ) ){
						unset( $tags[ $k ] );
						continue;
					}
				}

				if( ! $tags ) return;

				// set patterns from given $tags
				// separate classes & tags & set
				$class_patt = $tag_patt = $level_tags = array();
				foreach( $tags as $tag ){
						// class
					if( $tag{0} == '.' ){
						$tag  = substr( $tag, 1 );
						$link = & $class_patt;
					}
						// html tag
					else
						$link = & $tag_patt;

					$link[] = $tag;
					$level_tags[] = $tag;
				}

				$this->temp->level_tags = array_flip( $level_tags );

				// replace all titles & collect contents to $this->contents
				$patt_in = array();
				if( $tag_patt )   $patt_in[] = '(?:<('. implode('|', $tag_patt) .')([^>]*)>(.*?)<\/\1>)';
					if( $class_patt ) $patt_in[] = '(?:<([^ >]+) ([^>]*class=["\'][^>]*('. implode('|', $class_patt) .')[^>]*["\'][^>]*)>(.*?)<\/'. ($patt_in?'\4':'\1') .'>)';

						$patt_in = implode('|', $patt_in );

						$this->temp->content = $content;

				// collect and replace
						$_content = preg_replace_callback("/$patt_in/is", array( &$this, '_make_contents_callback'), $content, -1, $count );

						if( ! $count || $count < $this->opt->min_found ){
						unset($this->temp); // clear cache
						return;
					}

				$this->temp->content = $content = $_content; // $_content was for check reasone

				// html
				static $css, $js;
				$embed   = isset($this->temp->embed);
				$_tit    = & $this->opt->title;
				$_is_tit = ! $embed && $_tit;

				// markup
				$ItemList = $this->opt->markup ? ' itemscope itemtype="https://schema.org/ItemList"' : '';

				$contents =
				( ( ! $css && $this->opt->css ) ? '<style>'. preg_replace('/[\n\t ]+/', ' ', $this->opt->css ) .'</style>' : '' ) .
				( $_is_tit ? '<div class="block-toc"'. $ItemList .' >' : '' ) .
				( $_is_tit ? '<span class="block-toc-title" id="kcmenu"'. ($ItemList?' itemprop="name"':'') .'>'. $_tit .'</span>'. "\n" : '' ) .
				'<ul class="block-toc-contents"'. ( (! $_tit || $embed) ? ' id="kcmenu"' : '' ) . ( ($ItemList && ! $_is_tit ) ? $ItemList : '' ) .'>'. "\n".
				implode('', $this->contents ) .
				'</ul>'."\n" .
				( $_is_tit ? '</div>' : '' ) .
				( ( ! $js && $this->opt->js ) ? '<script>'. preg_replace('/[\n\t ]+/', ' ', $this->opt->js ) .'</script>' : '' ) ;

				unset($this->temp); // clear cache

				return $this->contents = $contents;
			}

		## callback function to replace and collect contents
			private function _make_contents_callback( $match ){
				$temp = & $this->temp;

				// it's only class selector in pattern
				if( count($match) == 5 ){
					$tag   = $match[1];
					$attrs = $match[2];
					$title = $match[4];

						$level_tag = $match[3]; // class_name
					}
				// it's found tag selector
					elseif( count($match) == 4 ){
						$tag   = $match[1];
						$attrs = $match[2];
						$title = $match[3];

						$level_tag = $tag;
					}
				// it's found class selector
					else{
						$tag   = $match[4];
						$attrs = $match[5];
						$title = $match[7];

						$level_tag = $match[6]; // class_name
					}

					$anchor = $this->_sanitaze_anchor( $title );
				$opt = $this->opt; // make live easier

				$level = @ $temp->level_tags[ $level_tag ];
				if( $level > 0 )
					$sub = ( $opt->margin ? ' style="margin-left:'. ($level*$opt->margin) .'px;"' : '') . ' class="sub sub_'. $level .'"';
				else
					$sub = ' class="top"';

				// collect contents
				// markup
				$_is_mark = $opt->markup;

				$temp->counter = empty($temp->counter) ? 1 : $temp->counter+1;

				// $title не может содержать A, IMG теги - удалим если надо...
				$cont_title = $title;
				if( false !== strpos($cont_title, '</a>') ) $cont_title = preg_replace('~<a[^>]+>|</a>~', '', $cont_title );
				if( false !== strpos($cont_title, '<img') ) $cont_title = preg_replace('~<img[^>]+>~', '', $cont_title );

				$this->contents[] = "\t".
				'<li'. $sub . ($_is_mark?' itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"':'') .'>
				<a class="block-toc-link" rel="nofollow"'. ($_is_mark?' itemprop="item"':'') .' href="'. $opt->page_url .'#'. $anchor .'">
				'.( $_is_mark ? '<span itemprop="name">'. $cont_title .'</span>' : $cont_title ).'
				</a>
				'.( $_is_mark ? ' <meta itemprop="position" content="'. $temp->counter .'" />':'' ).'
				</li>'. "\n";

				if( $opt->anchor_link )
					$title = '<a rel="nofollow" class="kc__anchlink" href="#'. $anchor .'">'. $opt->anchor_link .'</a> ' . $title;

				$new_el = "\n<$tag id=\"$anchor\" $attrs>$title</$tag>";
				if( $opt->anchor_type == 'a' )
					$new_el = '<a class="kc__anchor" name="'. $anchor .'"></a>'."\n<$tag $attrs>$title</$tag>";

				$to_menu = '';
				if( $opt->to_menu ){
						// go to contents
					$to_menu = '<a rel="nofollow" class="kc-gotop kc__gotop" href="'. $opt->page_url .'#kcmenu">'. $opt->to_menu .'</a>';

						// remove '$to_menu' if simbols beatween $to_menu too small (< 300)
						$pos = strpos( $temp->content, $match[0] ); // mb_strpos( $temp->content, $match[0] ) - в 150 раз медленнее!
						if( empty($temp->elpos) ){
							$prevpos = 0;
							$temp->elpos = array( $pos );
						}
						else {
							$prevpos = end($temp->elpos);
							$temp->elpos[] = $pos;
						}
						$simbols_count = $pos - $prevpos;
						if( $simbols_count < $opt->tomenu_simcount ) $to_menu = '';
					}

					return $to_menu . $new_el;
				}

		## URL transliteration
				function _sanitaze_anchor( $anch ){
					$anch = strip_tags( $anch );

					$iso9 = array(
						'А'=>'A', 'Б'=>'B', 'В'=>'V', 'Г'=>'G', 'Д'=>'D', 'Е'=>'E', 'Ё'=>'YO', 'Ж'=>'ZH',
						'З'=>'Z', 'И'=>'I', 'Й'=>'J', 'К'=>'K', 'Л'=>'L', 'М'=>'M', 'Н'=>'N', 'О'=>'O',
						'П'=>'P', 'Р'=>'R', 'С'=>'S', 'Т'=>'T', 'У'=>'U', 'Ф'=>'F', 'Х'=>'H', 'Ц'=>'TS',
						'Ч'=>'CH', 'Ш'=>'SH', 'Щ'=>'SHH', 'Ъ'=>'', 'Ы'=>'Y', 'Ь'=>'', 'Э'=>'E', 'Ю'=>'YU', 'Я'=>'YA',
						// small
						'а'=>'a', 'б'=>'b', 'в'=>'v', 'г'=>'g', 'д'=>'d', 'е'=>'e', 'ё'=>'yo', 'ж'=>'zh',
						'з'=>'z', 'и'=>'i', 'й'=>'j', 'к'=>'k', 'л'=>'l', 'м'=>'m', 'н'=>'n', 'о'=>'o',
						'п'=>'p', 'р'=>'r', 'с'=>'s', 'т'=>'t', 'у'=>'u', 'ф'=>'f', 'х'=>'h', 'ц'=>'ts',
						'ч'=>'ch', 'ш'=>'sh', 'щ'=>'shh', 'ъ'=>'', 'ы'=>'y', 'ь'=>'', 'э'=>'e', 'ю'=>'yu', 'я'=>'ya',
						// other
						'Ѓ'=>'G', 'Ґ'=>'G', 'Є'=>'YE', 'Ѕ'=>'Z', 'Ј'=>'J', 'І'=>'I', 'Ї'=>'YI', 'Ќ'=>'K', 'Љ'=>'L', 'Њ'=>'N', 'Ў'=>'U', 'Џ'=>'DH',
						'ѓ'=>'g', 'ґ'=>'g', 'є'=>'ye', 'ѕ'=>'z', 'ј'=>'j', 'і'=>'i', 'ї'=>'yi', 'ќ'=>'k', 'љ'=>'l', 'њ'=>'n', 'ў'=>'u', 'џ'=>'dh'
					);

					$anch = strtr( $anch, $iso9 );

					$spec = preg_quote( $this->opt->spec );
				$anch = preg_replace("/[^a-zA-Z0-9_$spec\-]+/", '-', $anch ); // все ненужное на '-'
				$anch = strtolower( trim( $anch, '-') );
				$anch = substr( $anch, 0, 70 ); // shorten
				$anch = $this->_unique_anchor( $anch );

				return $anch;
			}

		## adds number at the end if this anchor already exists
			function _unique_anchor( $anch ){
				$temp = & $this->temp;

				// check and unique anchor
				if( empty($temp->anchors) ){
					$temp->anchors = array( $anch => 1 );
				}
				elseif( isset($temp->anchors[ $anch ]) ){
					$lastnum = substr( $anch, -1 );
					$lastnum = is_numeric($lastnum) ? $lastnum + 1 : 2;
					return $this->_unique_anchor( "$anch-$lastnum" );
				}
				else {
					$temp->anchors[ $anch ] = 1;
				}

				return $anch;
			}

		## cut the shortcode from the content
			function strip_shortcode( $text ){
				return preg_replace('~\['. $this->opt->shortcode .'[^\]]*\]~', '', $text );
			}
		}

/**
 * 3.14 - баг с дублированием 'anchor_link'
 * 3.13 - новый параметр 'js'
 * 3.12 - уникализация одинаковых якорей - _unique_anchor()
 * 3.11 - удаляется IMG тег из заголовка в оглавлении...
 * 3.10 - удаляется A тег из заголовка в оглавлении...
 * 3.9 - при 'anchor_type=a' не работал параметр 'anchor_link'
 * 3.8 - баг синтаксиса при заполнении свойства $this->contents в PHP 7.1
 * 3.7 - добавил элемент position при маркировке schema.org
 * 3.6.1 - тег заголовка "Содержание" изменил с DIV на SPAN
 * 3.6 - исправление парсинга тегов - удаление пустых при разбиении по [ ,]
 * 3.5 - стабильность. в параметр selectors можно указывать строку с элементами через запятую.
 * 3.4 - параметр 'tomenu_simcount'
 * 3.3 - smart 'to contents' link show - not show next link if symbols between prev smaller than 500
 */