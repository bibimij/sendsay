<?php

/**
 * Библиотека Sendsay API.
 *
 * @version 1.0
 * @author  Alex Milekhin (me@alexmil.ru)
 * @link    [https://pro.subscribe.ru/API/API.html][Документация]
 */
class Sendsay
{	
	/**
	 * @var массив с авторизационными данными
	 */
	private $auth = array();
	
	/**
	 * @var параметры запроса
	 */
	private $params;
	
	/**
	 * @var вывод отладочной информации
	 */
	public $debug = FALSE;

	
	
	/**
	 * Конструктор класса.
	 * 
	 * @param  string  общий логин
	 * @param  string  личный логин
	 * @param  string  пароль
	 * @param  bool    вывод отладочной информации
	 */
	public function Sendsay($login, $sublogin, $password, $debug=FALSE)
	{
		$this->debug = $debug;
		$this->auth['one_time_auth'] = array(
			'login'    => $login,
			'sublogin' => $sublogin,
			'passwd'   => $password
		);
	}
	
	/**
	 * Проверяет доступность сервера Sendsay.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%9F%D0%B8%D0%BD%D0%B3-%D0%B1%D0%B5%D0%B7-%D0%B0%D0%B2%D1%82%D0%BE%D1%80%D0%B8%D0%B7%D0%B0%D1%86%D0%B8%D0%B8][Документация]
	 * 
	 * @return bool
	 */
	public function ping()
	{
		$this->params['action'] = 'ping';
		
		$result = $this->send();
		
		return isset($result['pong']);
	}
	
	/**
	 * Пинг с авторизацией.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%9F%D0%B8%D0%BD%D0%B3-%D1%81-%D0%B0%D0%B2%D1%82%D0%BE%D1%80%D0%B8%D0%B7%D0%B0%D1%86%D0%B8%D0%B5%D0%B9][Документация]
	 * 
	 * @return bool
	 */
	public function pong()
	{
		$this->params = $this->auth+array(
			'action' => 'pong'
		);
		
		$result = $this->send();
		
		return isset($result['ping']);
	}
	
	/**
	 * Проверяет список адресов на синтаксическую верность, доступность и возвращает нормализованый вариант написания.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%9F%D1%80%D0%BE%D0%B2%D0%B5%D1%80%D0%BA%D0%B0-%D0%B0%D0%B4%D1%80%D0%B5%D1%81%D0%BE%D0%B2][Документация]
	 * 
	 * @param  array  список емэйлов
	 * @param  int    проверять доступность по smtp (1) или нет (0)
	 * @param  int    таймаут в секундах
	 * 
	 * @return array
	 */
	public function email_test($list, $smtp=0, $timeout=15)
	{
		$this->params = $this->auth+array(
			'action'       => 'email.test',
			'smtp.test'    => $smtp,
			'smtp.timeout' => $timeout,
			'list'         => $list
		);
		
		return $this->send();
	}

	/**
	 * Запрашивает ответы подписчика.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%9F%D0%BE%D0%BB%D1%83%D1%87%D0%B8%D1%82%D1%8C-%D0%BE%D1%82%D0%B2%D0%B5%D1%82%D1%8B-%D0%BF%D0%BE%D0%B4%D0%BF%D0%B8%D1%81%D1%87%D0%B8%D0%BA%D0%B0][Документация]
	 * 
	 * @param  string  емэйл подписчика
	 * 
	 * @return array
	 */
	public function member_get($email)
	{
		$this->params = $this->auth+array(
			'action' => 'member.get',
			'email' => $email
		);

		return $this->send();
	}
	
	/**
	 * Добавляет нового подписчика или обновляет существующего.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#C%D0%BE%D0%B7%D0%B4%D0%B0%D1%82%D1%8C-%D0%BF%D0%BE%D0%B4%D0%BF%D0%B8%D1%81%D1%87%D0%B8%D0%BA%D0%B0-%D0%A3%D1%81%D1%82%D0%B0%D0%BD%D0%BE%D0%B2%D0%B8%D1%82%D1%8C-%D0%BE%D1%82%D0%B2%D0%B5%D1%82%D1%8B-%D0%BF%D0%BE%D0%B4%D0%BF%D0%B8%D1%81%D1%87%D0%B8%D0%BA%D0%B0][Документация]
	 * 
	 * @param  string  емэйл подписчика
	 * @param  array   массив с данными подписчика
	 * @param  mixed   код шаблона письма-приветствия (int) или не высылать письмо (FALSE)
	 * @param  int     необходимость подтверждения внесения в базу
	 * @param  string  правило изменения ответов анкетных данных (error|update|overwrite)
	 * @param  string  тип адреса подписчика (email|msisdn)
	 * 
	 * @return array
	 */
	public function member_set($email, $data=NULL, $notify=FALSE, $confirm=FALSE, $if_exists='overwrite', $addr_type='email')
	{
		$this->params = $this->auth+array(
			'action'                   => 'member.set',
			'addr_type'                => $addr_type,
			'email'                    => $email,
			'source'                   => $_SERVER['REMOTE_ADDR'],
			'if_exists'                => $if_exists,
			'newbie.letter.no-confirm' => $notify,
			'newbie.confirm'           => $confirm,
		);
		
		$this->param('obj', $data);

		return $this->send();
	}
	
	/**
	 * Удаляет пользователя из списка рассылки.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A3%D0%B4%D0%B0%D0%BB%D0%B8%D1%82%D1%8C-%D0%BF%D0%BE%D0%B4%D0%BF%D0%B8%D1%81%D1%87%D0%B8%D0%BA%D0%B0][Документация]
	 * 
	 * @param  mixed  строка или массив с емэйл-адресами подписчиков
	 * @param  bool   флаг асинхронного запуска
	 * 
	 * @return array
	 */
	public function unsubscribe($email, $sync=FALSE)
	{
		$this->params = $this->auth+array(
			'action' => 'member.delete',
			'list'   => is_array($email) ? $email : array($email),
			'sync'   => $sync
		);
		
		return $this->send();
	}
	
	/**
	 * Извлекает список выпусков в архиве.
	 * Входные параметры — необязательные фильтры.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A1%D0%BF%D0%B8%D1%81%D0%BE%D0%BA-%D0%B2%D1%8B%D0%BF%D1%83%D1%81%D0%BA%D0%BE%D0%B2-%D0%B2-%D0%B0%D1%80%D1%85%D0%B8%D0%B2%D0%B5][Документация]
	 * 
	 * @param  string  начиная с даты (формат YYYY-MM-DD)
	 * @param  string  заканчивая датой (формат YYYY-MM-DD)
	 * @param  array   массив с идентификаторами групп
	 * 
	 * @return array
	 */
	public function issue_list($from='1900-01-01', $to=NULL, $groups=array())
	{
		$this->params = $this->auth+array(
			'action' => 'issue.list',
			'from'   => $from,
			'group'  => $groups
		);
		
		$this->param('upto', $to);
		
		return $this->send();
	}
	
	/**
	 * Извлекает информацию о выпуске в архиве.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A7%D1%82%D0%B5%D0%BD%D0%B8%D0%B5-%D0%B2%D1%8B%D0%BF%D1%83%D1%81%D0%BA%D0%B0-%D0%B2-%D0%B0%D1%80%D1%85%D0%B8%D0%B2%D0%B5][Документация]
	 * 
	 * @param  int  уникальный идентификатор выпуска
	 * 
	 * @return array
	 */
	public function issue_get($id)
	{
		$this->params = $this->auth+array(
			'action' => 'issue.get',
			'id'     => $id
		);
		
		return $this->send();
	}
	
	/**
	 * Извлекает статистику активности подписчиков.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A1%D1%82%D0%B0%D1%82%D0%B8%D1%81%D1%82%D0%B8%D0%BA%D0%B0-%D0%B0%D0%BA%D1%82%D0%B8%D0%B2%D0%BD%D0%BE%D1%81%D1%82%D0%B8-%D0%BF%D0%BE%D0%B4%D0%BF%D0%B8%D1%81%D1%87%D0%B8%D0%BA%D0%BE%D0%B2][Документация]
	 * 
	 * @return array
	 */
	public function stat_activity($select, $gid=NULL, $limit=NULL, $page=NULL, $sort='date',
		$desc=0, $from=NULL, $to=NULL, $issue_from=NULL, $issue_to=NULL, $result='save',
		$email=array(), $format='csv')
	{
		$this->params = $this->auth+$select+array(
			'action' => 'stat.activity',
			'sort'   => $sort,
			'desc'   => $desc,
			'result' => $result
		);
		
		$this->param('gid', $gid);
		$this->param('limit', $limit);
		$this->param('page', $page);
		$this->param('from', $from);
		$this->param('to', $to);
		$this->param('issue_from', $issue_from);
		$this->param('issue_to', $issue_to);
		
		switch ($result)
		{
			case 'email':
				$this->params['email'] = $email;
			case 'save':
				$this->params['result.format'] = $format;
		}
		
		return $this->send();
	}
	
	/**
	 * Возвращает статистику по выпускам.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A1%D1%82%D0%B0%D1%82%D0%B8%D1%81%D1%82%D0%B8%D0%BA%D0%B0-%D0%B2%D1%8B%D0%BF%D1%83%D1%81%D0%BA%D0%BE%D0%B2][Документация]
	 * 
	 * @param  string  начиная с даты (формат YYYY-MM-DD)
	 * @param  string  заканчивая датой (формат YYYY-MM-DD)
	 * @param  array   массив с идентификаторами групп
	 * @param  string  способ группировки по времени
	 * @param  string  итог по всем записям (none — не нужен | yes  — нужен | only — только он и нужен)
	 * @param  int     выводить (1) или нет (0) статистику по тем группам подписчиков, по которым не было отправлено ни одного выпуска
	 * 
	 * @return array
	 */
	public function stat_issue($from=NULL, $to=NULL, $groups=array(), $groupby='YM', $total='none', $withempty=0)
	{
		$this->params = $this->auth+array(
			'action'     => 'stat.issue',
			'group'      => $groups,
			'groupby'    => $groupby,
			'total'      => $total,
			'withempty'  => $withempty
		);
		
		$this->param('issue.from', $from);
		$this->param('issue.upto', $to);
		
		return $this->send();
	}
	
	/**
	 * Универсальная функция извлечения статистики.
	 * Позволяет получить информацию про переходы, открытия писем, тиражи выпусков и результаты доставки.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A3%D0%BD%D0%B8%D0%B2%D0%B5%D1%80%D1%81%D0%B0%D0%BB%D1%8C%D0%BD%D0%B0%D1%8F-%D1%81%D1%82%D0%B0%D1%82%D0%B8%D1%81%D1%82%D0%B8%D0%BA%D0%B0][Документация]
	 * 
	 * @param  array  список полей и функций для выборки
	 * @param  array  фильтр результатов
	 * @param  array  сортировка результата
	 * @param  mixed  способ возврата результата; строка с типом или массив с емэйлами получателей
	 * @param  string формат вывода
	 * @param  int    число пропускаемых от начала строк данных отчёта; по умолчанию — 0
	 * @param  int    число выбираемых строк после пропуска skip; по умолчанию — все
	 * 
	 * @return array
	 */
	public function stat_uni($select, $filter=array(), $order=array(), $result='save', $format='csv', $skip=0, $count=NULL)
	{
		$this->params = $this->auth+array(
			'action' => 'stat.uni',
			'skip'   => $skip,
			'select' => $select,
			'order'  => $order,
			'filter' => $filter,
			'result' => is_array($result) ? 'email' : $result
		);
		
		$this->param('first', $count);
		
		switch ($this->params['result'])
		{
			case 'email':
				$this->params['email'] = $result;
			case 'save':
				$this->params['result.format'] = $format;
		}
		
		return $this->send();
	}
	
	/**
	 * Возвращает список групп.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#C%D0%BF%D0%B8%D1%81%D0%BE%D0%BA-%D0%B3%D1%80%D1%83%D0%BF%D0%BF][Документация]
	 * 
	 * @return array
	 */
	public function group_list()
	{
		$this->params = $this->auth+array(
			'action' => 'group.list'
		);
		
		return $this->send();
	}
	
	/**
	 * Возвращает общую статистику по группе.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%9E%D0%B1%D1%89%D0%B0%D1%8F-%D1%81%D1%82%D0%B0%D1%82%D0%B8%D1%81%D1%82%D0%B8%D0%BA%D0%B0-%D0%BF%D0%BE-%D0%B3%D1%80%D1%83%D0%BF%D0%BF%D0%B5][Документация]
	 * 
	 * @param  array   коды групп; если пусто - по всем
	 * @param  string  cпособ возврата результата (response|email|save)
	 * @param  array   адреса получателей отчетов
	 * @param  string  формат файла с данными (csv|xlsx)
	 * 
	 * @return array
	 */
	public function stat_group_common($groups=array(), $result='response', $email=array(), $format='csv')
	{
		$this->params = $this->auth+array(
			'action' => 'stat.group.common',
			'group'  => $groups,
			'result' => $result
		);
		
		switch ($result)
		{
			case 'email':
				$this->params['email'] = $email;
			case 'save':
				$this->params['result.format'] = $format;
		}
		
		return $this->send();
	}
	
	/**
	 * Импотирует список подписчиков.
	 * В случае указания ссылки на список подписчиков, файл должен быть в UTF-8, а поля разделяться запятыми (стандарт CSV).
	 * Первой строкой или элементом массива идёт заголовок.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%92%D0%BD%D0%B5%D1%81%D0%B5%D0%BD%D0%B8%D0%B5-%D1%81%D0%BF%D0%B8%D1%81%D0%BA%D0%B0-%D0%BF%D0%BE%D0%B4%D0%BF%D0%B8%D1%81%D1%87%D0%B8%D0%BA%D0%BE%D0%B2][Документация]
	 * 
	 * @param  mixed   список подписчиков
	 *                 string — ссылка на файл с подписчиками;
	 *                 integer — идентификатор уже загруженных данных;
	 *                 array — массив подписчиков;
	 * @param  array   группа импорта подписчиков
	 * @param  string  действие, если адрес существует
	 * @param  bool    срабатывание триггеров
	 * @param  string  дополнить данными из формата
	 * @param  string  номер шаблона письма
	 * @param  string  тип вносимых адресов
	 * 
	 * @return array
	 */
	public function member_import($data, $group=NULL, $exist='overwrite', $trigger=TRUE, $format=NULL, $confirm=NULL, $addr_type='email')
	{
		$this->params = $this->auth+array(
			'action'         => 'member.import',
			'addr_type'      => $addr_type,
			'firstline'      => 1,
			'if_exists'      => $exist,
			'separator'      => ',',
			'charset'        => 'utf-8',
			'sequence.event' => $trigger ? 1 : 0,
			'newbie.confirm' => $confirm ? 1 : 0
		);
		
		if (gettype($data) == 'string')
		{
			$this->params['users.url'] = $data;
		}
		elseif (gettype($data) == 'integer')
		{
			$this->params['uid'] = $data;
		}
		else
		{
			$this->params['users.list'] = json_encode($data);
		}
		
		$this->param('auto_group', $group);
		$this->param('format', $format);
		$this->param('letter', $letter);
		$this->param('newbie.letter.confirm', $confirm);
		
		return $this->send();
	}

	/**
	 * Чтение черновика.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A7%D1%82%D0%B5%D0%BD%D0%B8%D0%B5-%D1%87%D0%B5%D1%80%D0%BD%D0%BE%D0%B2%D0%B8%D0%BA%D0%B0][Документация]
	 * 
	 * @param  int  код черновика
	 * 
	 * @return array
	 */
	public function issue_draft_get($id)
	{
		$this->params = $this->auth+array(
			'action' => 'issue.draft.get',
			'id'     => $id
		);
		
		return $this->send();
	}
	
	/**
	 * Создает или изменяет параметры и содержимое черновиков. Вызов не может быть применён к предустановленным черновикам.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A1%D0%BE%D0%B7%D0%B4%D0%B0%D0%BD%D0%B8%D0%B5-%D0%B8%D0%BB%D0%B8-%D0%B8%D0%B7%D0%BC%D0%B5%D0%BD%D0%B5%D0%BD%D0%B8%D0%B5-%D1%87%D0%B5%D1%80%D0%BD%D0%BE%D0%B2%D0%B8%D0%BA%D0%B0][Документация]
	 * 
	 * @param  int     код черновика
	 * @param  string  название черновика
	 * @param  string  имя отправителя
	 * @param  string  емэйл отправителя
	 * @param  string  тема письма
	 * @param  string  содержимое письма
	 * @param  string  формат черновика (html|sms|text)
	 * @param  bool    вернуть параметры черновика
	 * 
	 * @return array
	 */
	public function issue_draft_set($id=NULL, $name=NULL, $from=NULL, $sender=NULL, $subject=NULL, $text=NULL, $format='html', $return=NULL)
	{
		$this->params = $this->auth+array(
			'action' => 'issue.draft.set',
			'obj'    => array(
				'name'    => $name,
				'from'    => $from,
				'sender'  => $sender,
				'subject' => $subject,
				'text'    => $text,
				'format'  => $format
			)
		);

		$this->param('id', $id);
		$this->param('return_fresh_obj', $return);
		
		return $this->send();
	}

	/**
	 * Удаление черновика.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A3%D0%B4%D0%B0%D0%BB%D0%B5%D0%BD%D0%B8%D0%B5-%D1%87%D0%B5%D1%80%D0%BD%D0%BE%D0%B2%D0%B8%D0%BA%D0%B0][Документация]
	 * 
	 * @param  mixed  код черновика (int) или список черновиков (array) к удалению
	 * 
	 * @return array
	 */
	public function issue_draft_delete($ids)
	{
		$this->params = $this->auth+array(
			'action' => 'issue.draft.delete',
			'id'     => $ids
		);
		
		return $this->send();
	}

	/**
	 * Асинхронно отправляет выпуск.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%9E%D1%82%D0%BE%D1%81%D0%BB%D0%B0%D1%82%D1%8C-%D0%B2%D1%8B%D0%BF%D1%83%D1%81%D0%BA][Документация]
	 * 
	 * @param  string  способ выпуска (код группы | masssending - экспресс-выпуск | personal - транзакционное письмо)
	 * @param  mixed   код шаблона (int) или емэйл отправителя (string)
	 * @param  string  имя отправителя (string) или массив экстра данных (array)
	 * @param  string  тема письма
	 * @param  string  содержимое письма
	 * @param  array   список получателей
	 * @param  array   параметры преобразования ссылок для учёта перехода по ним
	 * @param  string  когда выпустить (now - сейчас | save - отложить на хранение)
	 * @param  string  формат содержимого (html|text)
	 * 
	 * @return array
	 */
	public function issue_send($group, $from, $sender='', $subject='', $text='', $users_list=NULL, $relink=array(), $sendwhen='now', $format='html')
	{
		$this->params = $this->auth+array(
			'action'       => 'issue.send',
			'group'        => $group,
			'letter' => array(
				'draft.id'   => is_int($from) ? $from : NULL,
				'from.email' => $from,
				'from.name'  => $sender,
				'subject'    => $subject,
				'message'    => array($format => $text)
			),
			'sendwhen'     => $sendwhen,
			'relink'       => is_null($relink) ? 0 : 1,
			'relink.param' => is_null($relink) ? array() : array_merge(array('link' => 1, 'image' => 0, 'test' => 1), $relink)
		);

		if (is_array($sender))
		{
			$this->params['extra'] = $sender;
		}

		$this->param('users.list', $users_list);
		
		return $this->send();
	}

	/**
	 * Завершает прохождение подписчиками последовательности.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%9F%D1%80%D0%B5%D1%80%D0%B2%D0%B0%D1%82%D1%8C-%D0%BF%D1%80%D0%BE%D1%85%D0%BE%D0%B6%D0%B4%D0%B5%D0%BD%D0%B8%D0%B5-%D0%BF%D0%BE%D1%81%D0%BB%D0%B5%D0%B4%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D1%8C%D0%BD%D0%BE%D1%81%D1%82%D0%B8][Документация]
	 * 
	 * @param  int    код последовательности
	 * @param  mixed  код группы (string) или список емэйлов (array)
	 * 
	 * @return array
	 */
	public function sequence_member_stop($id, $data)
	{
		$this->params = $this->auth+array(
			'action'       => 'sequence.member.stop',
			'id'           => $id
		);

		if (gettype($data) == 'string')
		{
			$this->params['group'] = $data;
		}
		elseif (gettype($data) == 'array')
		{
			$this->params['list'] = $data;
		}
		
		return $this->send();
	}

	/**
	 * Загружает картинку на сервер.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%97%D0%B0%D0%BF%D0%B8%D1%81%D0%B0%D1%82%D1%8C-%D1%84%D0%B0%D0%B9%D0%BB][Документация]
	 * 
	 * @param  string  расположение загружаемого файла
	 * @param  string  директория загрузки файла с именем файла (несуществующие каталоги не создаются)
	 * 
	 * @return array
	 */
	public function put_file($from, $to)
	{
		$this->params = $this->auth+array(
			'action'   => 'rfs.file.put',
			'domain'   => 'image',
			'encoding' => 'base64',
			'data'     => base64_encode(file_get_contents($from)),
			'path'     => $to
		);
		
		return $this->send();
	}

	/**
	 * Создаёт каталог.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#C%D0%BE%D0%B7%D0%B4%D0%B0%D1%82%D1%8C-%D0%BA%D0%B0%D1%82%D0%B0%D0%BB%D0%BE%D0%B3][Документация]
	 *
	 * @param  string  полный путь с названием каталога (несуществующие каталоги создаются)
	 * 
	 * @return array
	 */
	public function mkdir($path)
	{
		$this->params = $this->auth+array(
			'action' => 'rfs.dir.make',
			'domain' => 'image',
			'path'   => $path
		);
		
		return $this->send();
	}

	/**
	 * Удаляет каталог.
	 * Примечание: католог должен быть пустым.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A3%D0%B4%D0%B0%D0%BB%D0%B8%D1%82%D1%8C-%D0%BA%D0%B0%D1%82%D0%B0%D0%BB%D0%BE%D0%B3][Документация]
	 *
	 * @param  string  полный путь с названием каталога
	 * 
	 * @return array
	 */
	public function rm($path)
	{
		$this->params = $this->auth+array(
			'action' => 'rfs.dir.delete',
			'domain' => 'image',
			'path'   => $path
		);
		
		return $this->send();
	}

	/**
	 * Возвращает список настроек.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%9F%D0%BE%D0%BB%D1%83%D1%87%D0%B8%D1%82%D1%8C-%D0%BD%D0%B0%D1%81%D1%82%D1%80%D0%BE%D0%B9%D0%BA%D0%B8][Документация]
	 * 
	 * @return array
	 */
	public function sys_settings_get()
	{
		$this->params = $this->auth+array(
			'action' => 'sys.settings.get'
		);
		
		return $this->send();
	}

	/**
	 * Сохраняет настройки.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%9F%D0%BE%D0%BC%D0%B5%D0%BD%D1%8F%D1%82%D1%8C-%D0%BD%D0%B0%D1%81%D1%82%D1%80%D0%BE%D0%B9%D0%BA%D0%B8][Документация]
	 * 
	 * @param  array  массив изменяемых параметров
	 *
	 * @return array
	 */
	public function sys_settings_set($options)
	{
		$this->params = $this->auth+array(
			'action' => 'sys.settings.set',
			'list'   => $options
		);
		
		return $this->send();
	}

	/**
	 * Возвращает список пользователей.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A1%D0%BF%D0%B8%D1%81%D0%BE%D0%BA-%D0%BF%D0%BE%D0%BB%D1%8C%D0%B7%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D0%B5%D0%B9][Документация]
	 *
	 * @return array
	 */
	public function user_list()
	{
		$this->params = $this->auth+array(
			'action' => 'user.list'
		);
		
		return $this->send();
	}

	/**
	 * Создаёт пользователя.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A1%D0%BE%D0%B7%D0%B4%D0%B0%D0%BD%D0%B8%D0%B5-%D0%BF%D0%BE%D0%BB%D1%8C%D0%B7%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D1%8F][Документация]
	 *
	 * @param  string  саблогин
	 * @param  string  пароль
	 * @param  string  адрес получателя письма с данными пользователя
	 *
	 * @return array
	 */
	public function user_create($login, $password, $email=NULL)
	{
		$this->params = $this->auth+array(
			'action'   => 'user.create',
			'sublogin' => $login,
			'password' => $password
		);

		$this->param('email', $email);
		
		return $this->send();
	}

	/**
	 * Удаляет пользователя.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A3%D0%B4%D0%B0%D0%BB%D0%B5%D0%BD%D0%B8%D0%B5-%D0%BF%D0%BE%D0%BB%D1%8C%D0%B7%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D1%8F][Документация]
	 *
	 * @param  string  саблогин
	 *
	 * @return array
	 */
	public function user_delete($login)
	{
		$this->params = $this->auth+array(
			'action'   => 'user.delete',
			'sublogin' => $login
		);
		
		return $this->send();
	}

	/**
	 * Изменяет пароль и статус пользователя.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%98%D0%B7%D0%BC%D0%B5%D0%BD%D0%B5%D0%BD%D0%B8%D0%B5-%D0%BF%D0%B0%D1%80%D0%BE%D0%BB%D1%8F-%D0%B8-%D1%81%D1%82%D0%B0%D1%82%D1%83%D1%81%D0%B0-%D0%BB%D1%8E%D0%B1%D0%BE%D0%B3%D0%BE-%D0%BF%D0%BE%D0%BB%D1%8C%D0%B7%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D1%8F][Документация]
	 *
	 * @param  string  саблогин
	 * @param  int     состояние пользователя (−1 — заставить сменить пароль | 0 — активировать | 1 — заблокировать)
	 * @param  string  старый пароль
	 * @param  string  новый пароль
	 * @param  string  адрес получателя письма с данными пользователя
	 *
	 * @return array
	 */
	public function user_set($login, $status, $old_password=NULL, $new_password=NULL, $email=NULL)
	{
		$this->params = $this->auth+array(
			'action'       => 'user.set',
			'sublogin'     => $login,
			'status'       => $status,
			'password.old' => $old_password,
			'password.new' => $new_password
		);

		$this->param('email', $email);
		
		return $this->send();
	}

	/**
	 * Изменяет пароль текущего пользователя.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%98%D0%B7%D0%BC%D0%B5%D0%BD%D0%B5%D0%BD%D0%B8%D0%B5-%D0%BF%D0%B0%D1%80%D0%BE%D0%BB%D1%8F-%D1%81%D0%B5%D0%B1%D0%B5][Документация]
	 *
	 * @param  string  старый пароль
	 * @param  string  новый пароль
	 *
	 * @return array
	 */
	public function sys_password_set($old_password, $new_password)
	{
		$this->params = $this->auth+array(
			'action'       => 'sys.password.set',
			'password.old' => $old_password,
			'password.new' => $new_password
		);
		
		return $this->send();
	}

	/**
	 * Отправляет сообщение в техподдержку.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%9E%D0%B1%D1%80%D0%B0%D1%89%D0%B5%D0%BD%D0%B8%D0%B5-%D0%B2-%D1%81%D0%B0%D0%BF%D0%BF%D0%BE%D1%80%D1%82][Документация]
	 *
	 * @param  string  емэйл для связи
	 * @param  string  текст сообщения
	 *
	 * @return array
	 */
	public function sys_message($email, $text)
	{
		$this->params = $this->auth+array(
			'action' => 'sys.message',
			'email'  => $email,
			'text'   => $text
		);
		
		return $this->send();
	}

	/**
	 * Запрашивает лог активности аккаунта.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%96%D1%83%D1%80%D0%BD%D0%B0%D0%BB-%D1%80%D0%B0%D0%B1%D0%BE%D1%82%D1%8B][Документация]
	 *
	 * @param  datetime  дата события от
	 * @param  datetime  дата события по
	 *
	 * @return array
	 */
	public function sys_log($from=NULL, $to=NULL)
	{
		$this->params = $this->auth+array(
			'action' => 'sys.log'
		);

		$this->param('from', $from);
		$this->param('upto', $to);
		
		return $this->send();
	}

	/**
	 * Форматирует JSON-строку для отладки.
	 *
	 * @param  string  исходная JSON-строка
	 *
	 * @return string
	 */
	private function json_dump($json)
	{
		$result      = '';
		$pos         = 0;
		$strLen      = strlen($json);
		$indentStr   = "\t";
		$newLine     = "\n";
		$prevChar    = '';
		$outOfQuotes = true;
	
		for ($i = 0; $i <= $strLen; $i++)
		{
			$char = substr($json, $i, 1);

			if ($char == '"' && $prevChar != '\\')
			{
				$outOfQuotes = !$outOfQuotes;
			}
			elseif (($char == '}' || $char == ']') && $outOfQuotes)
			{
				$result .= $newLine;
				$pos--;
				for ($j=0; $j<$pos; $j++)
				{
					$result .= $indentStr;
				}
			}
			
			$result .= $char;

			if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes)
			{
				$result .= $newLine;
				if ($char == '{' || $char == '[')
				{
					$pos++;
				}
				
				for ($j = 0; $j < $pos; $j++)
				{
					$result .= $indentStr;
				}
			}
			
			$prevChar = $char;
		}
	
		return $result;
	}
	
	/**
	 * Добавляет значение к массиву параметров запроса.
	 * 
	 * @param  string название параметра
	 * @param  mixed  значение параметра
	 */
	private function param($name, $value=NULL)
	{
		if ($value !== NULL)
		{
			$this->params[$name] = $value;
		}
	}
	
	/**
	 * Отправляет данные в Sendsay.
	 * 
	 * @return array
	 */
	private function send($redirect = '')
	{
		if ($this->debug)
		{
			echo '<pre>Запрос:'."\n".$this->json_dump(print_r(json_encode($this->params), TRUE))."\n";
		}
		
		$curl = curl_init('https://pro.subscribe.ru/api'.$redirect.'?apiversion=100&json=1');
		
		curl_setopt($curl, CURLOPT_POST, TRUE);
		curl_setopt($curl, CURLOPT_POSTFIELDS, 'request='.urlencode(json_encode($this->params)));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		
		$result = curl_exec($curl);
		$json = json_decode($result, TRUE);
		
		if ($this->debug)
		{
			echo 'Ответ:'."\n".$this->json_dump($result).'</pre>';
		}
		
		curl_close($curl);
		
		if ( ! $json)
		{
			return array('error' => 'error/bad_json', 'explain' => $result);
		}

		if (array_key_exists('REDIRECT', $json))
		{
			return $this->send($json['REDIRECT']);
		}
		
		return $json;
	}
}

/**
 * Создаёт экземпляр класса Sendsay.
 * 
 * @param  string  общий логин
 * @param  string  личный логин
 * @param  string  пароль
 * @param  bool    вывод отладочной информации
 *
 * @return Sendsay
 */
function Sendsay($login, $sublogin, $password, $debug=FALSE)
{
	return new Sendsay($login, $sublogin, $password, $debug);
}


