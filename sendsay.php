<?php

/**
 * Библиотека Sendsay API.
 *
 * @version 1.4
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
	 * Возвращает список асинхронных запросов.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A1%D0%BF%D0%B8%D1%81%D0%BE%D0%BA-%D0%B0%D1%81%D0%B8%D0%BD%D1%85%D1%80%D0%BE%D0%BD%D0%BD%D1%8B%D1%85-%D0%B7%D0%B0%D0%BF%D1%80%D0%BE%D1%81%D0%BE%D0%B2][Документация]
	 * 
	 * @param  array  фильтр; массив должен содержать хотя бы один параметр
	 * 
	 * @return array
	 */
	public function track_list($filter)
	{
		$this->params = $this->auth+array(
			'action' => 'track.list',
			'filter' => $filter
		);
		
		return $this->send();
	}
	
	/**
	 * Возвращает описание асинхронного запроса.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%9E%D0%BF%D0%B8%D1%81%D0%B0%D0%BD%D0%B8%D0%B5-%D0%B0%D1%81%D0%B8%D0%BD%D1%85%D1%80%D0%BE%D0%BD%D0%BD%D0%BE%D0%B3%D0%BE-%D0%B7%D0%B0%D0%BF%D1%80%D0%BE%D1%81%D0%B0][Документация]
	 * 
	 * @param  int  код запроса
	 * 
	 * @return array
	 */
	public function track_get($id)
	{
		$this->params = $this->auth+array(
			'action' => 'track.get',
			'id'     => $id
		);
		
		return $this->send();
	}
	
	/**
	 * Возвращает список форматов и шаблонов.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A1%D0%BF%D0%B8%D1%81%D0%BE%D0%BA-%D1%84%D0%BE%D1%80%D0%BC%D0%B0%D1%82%D0%BE%D0%B2%D1%88%D0%B0%D0%B1%D0%BB%D0%BE%D0%BD%D0%BE%D0%B2][Документация]
	 * 
	 * @return array
	 */
	public function format_list()
	{
		$this->params = $this->auth+array(
			'action' => 'format.list'
		);
		
		return $this->send();
	}
	
	/**
	 * Создаёт или изменяет формат или шаблон.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A1%D0%BE%D0%B7%D0%B4%D0%B0%D0%BD%D0%B8%D0%B5-%D0%B8%D0%BB%D0%B8-%D0%B8%D0%B7%D0%BC%D0%B5%D0%BD%D0%B5%D0%BD%D0%B8%D0%B5-%D1%84%D0%BE%D1%80%D0%BC%D0%B0%D1%82%D0%B0%D1%88%D0%B0%D0%B1%D0%BB%D0%BE%D0%BD%D0%B0][Документация]
	 * 
	 * @param  array   данные формата (см. докумендацию)
	 * @param  string  код формата
	 * 
	 * @return array
	 */
	public function format_set($obj, $id=NULL)
	{
		$this->params = $this->auth+array(
			'action' => 'format.set',
			'obj'    => $obj
		);

		$this->param('id', $id);
		
		return $this->send();
	}
	
	/**
	 * Считывает формат или шаблон.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A7%D1%82%D0%B5%D0%BD%D0%B8%D0%B5-%D1%84%D0%BE%D1%80%D0%BC%D0%B0%D1%82%D0%B0%D1%88%D0%B0%D0%B1%D0%BB%D0%BE%D0%BD%D0%B0][Документация]
	 * 
	 * @param  string  код формата
	 * 
	 * @return array
	 */
	public function format_get($id)
	{
		$this->params = $this->auth+array(
			'action' => 'format.get',
			'id'     => $id
		);

		return $this->send();
	}
	
	/**
	 * Удаляет формат или шаблон.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A3%D0%B4%D0%B0%D0%BB%D0%B5%D0%BD%D0%B8%D0%B5-%D1%84%D0%BE%D1%80%D0%BC%D0%B0%D1%82%D0%B0%D1%88%D0%B0%D0%B1%D0%BB%D0%BE%D0%BD%D0%B0][Документация]
	 * 
	 * @param  string  код формата
	 * 
	 * @return array
	 */
	public function format_delete($id)
	{
		$this->params = $this->auth+array(
			'action' => 'format.delete',
			'id'     => $id
		);

		return $this->send();
	}
	
	/**
	 * Возвращает список анкет.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A1%D0%BF%D0%B8%D1%81%D0%BE%D0%BA-%D0%B0%D0%BD%D0%BA%D0%B5%D1%82][Документация]
	 * 
	 * @return array
	 */
	public function anketa_list()
	{
		$this->params = $this->auth+array(
			'action' => 'anketa.list'
		);
		
		return $this->send();
	}
	
	/**
	 * Возвращает данные анкеты.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A7%D1%82%D0%B5%D0%BD%D0%B8%D0%B5-%D0%B0%D0%BD%D0%BA%D0%B5%D1%82%D1%8B][Документация]
	 * 
	 * @param  string  код анкеты
	 * 
	 * @return array
	 */
	public function anketa_get($id)
	{
		$this->params = $this->auth+array(
			'action' => 'anketa.get',
			'id'     => $id
		);
		
		return $this->send();
	}
	
	/**
	 * Удаляет анкету.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A3%D0%B4%D0%B0%D0%BB%D0%B5%D0%BD%D0%B8%D0%B5-%D0%B0%D0%BD%D0%BA%D0%B5%D1%82%D1%8B][Документация]
	 * 
	 * @param  string  код анкеты
	 * 
	 * @return array
	 */
	public function anketa_delete($id)
	{
		$this->params = $this->auth+array(
			'action' => 'anketa.delete',
			'id'     => $id
		);
		
		return $this->send();
	}
	
	/**
	 * Создаёт анкету.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A1%D0%BE%D0%B7%D0%B4%D0%B0%D0%BD%D0%B8%D0%B5-%D0%B0%D0%BD%D0%BA%D0%B5%D1%82%D1%8B][Документация]
	 * 
	 * @param  string  название анкеты
	 * @param  string  код анкеты
	 * @param  string  код копируемой анкеты
	 * 
	 * @return array
	 */
	public function anketa_create($name, $id=NULL, $copy=NULL)
	{
		$this->params = $this->auth+array(
			'action' => 'anketa.create',
			'name'   => $name
		);

		$this->param('id', $id);
		$this->param('copy_from', $copy);
		
		return $this->send();
	}
	
	/**
	 * Изменяет название анкеты.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A1%D0%BE%D1%85%D1%80%D0%B0%D0%BD%D0%B5%D0%BD%D0%B8%D0%B5-%D0%B0%D0%BD%D0%BA%D0%B5%D1%82%D1%8B][Документация]
	 * 
	 * @param  string  код анкеты
	 * @param  string  название анкеты
	 * 
	 * @return array
	 */
	public function anketa_set($id, $name)
	{
		$this->params = $this->auth+array(
			'action' => 'anketa.set',
			'id'     => $id,
			'name'   => $name
		);
		
		return $this->send();
	}
	
	/**
	 * Добавляет вопрос в анкету.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%94%D0%BE%D0%B1%D0%B0%D0%B2%D0%BB%D0%B5%D0%BD%D0%B8%D1%8F-%D0%BD%D0%BE%D0%B2%D0%BE%D0%B3%D0%BE-%D0%B2%D0%BE%D0%BF%D1%80%D0%BE%D1%81%D0%B0-%D0%B0%D0%BD%D0%BA%D0%B5%D1%82%D1%8B][Документация]
	 * 
	 * @param  string  код анкеты
	 * @param  array   один или несколько вопросов анкеты
	 * 
	 * @return array
	 */
	public function anketa_quest_add($anketa, $questions)
	{
		$this->params = $this->auth+array(
			'action'    => 'anketa.quest.add',
			'anketa.id' => $anketa,
			'obj'       => $questions
		);
		
		return $this->send();
	}
	
	/**
	 * Изменяет вопросы анкеты.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%98%D0%B7%D0%BC%D0%B5%D0%BD%D0%B5%D0%BD%D0%B8%D0%B5-%D0%B2%D0%BE%D0%BF%D1%80%D0%BE%D1%81%D0%B0-%D0%B0%D0%BD%D0%BA%D0%B5%D1%82%D1%8B][Документация]
	 * 
	 * @param  string  код анкеты
	 * @param  array   один или несколько вопросов анкеты
	 * 
	 * @return array
	 */
	public function anketa_quest_set($anketa, $questions)
	{
		$this->params = $this->auth+array(
			'action'    => 'anketa.quest.set',
			'anketa.id' => $anketa,
			'obj'       => $questions
		);
		
		return $this->send();
	}
	
	/**
	 * Удаляет вопрос из анкеты.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A3%D0%B4%D0%B0%D0%BB%D0%B5%D0%BD%D0%B8%D0%B5-%D0%B2%D0%BE%D0%BF%D1%80%D0%BE%D1%81%D0%B0-%D0%B0%D0%BD%D0%BA%D0%B5%D1%82%D1%8B][Документация]
	 * 
	 * @param  string  код анкеты
	 * @param  mixed   один (string) или несколько (array) вопросов анкеты
	 * 
	 * @return array
	 */
	public function anketa_quest_delete($anketa, $questions)
	{
		$this->params = $this->auth+array(
			'action'    => 'anketa.quest.delete',
			'anketa.id' => $anketa,
			'id'        => $questions
		);
		
		return $this->send();
	}
	
	/**
	 * Изменяет порядок вопросов анкеты.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%98%D0%B7%D0%BC%D0%B5%D0%BD%D0%B5%D0%BD%D0%B8%D0%B5-%D0%BF%D0%BE%D0%B7%D0%B8%D1%86%D0%B8%D0%B8-%D0%B2%D0%BE%D0%BF%D1%80%D0%BE%D1%81%D0%B0-%D0%B0%D0%BD%D0%BA%D0%B5%D1%82%D1%8B][Документация]
	 * 
	 * @param  string  код анкеты
	 * @param  mixed   коды вопросов анкеты в нужном порядке
	 * 
	 * @return array
	 */
	public function anketa_quest_order($anketa, $order)
	{
		$this->params = $this->auth+array(
			'action'    => 'anketa.quest.order',
			'anketa.id' => $anketa,
			'order'     => $order
		);
		
		return $this->send();
	}
	
	/**
	 * Изменяет порядок ответов.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%98%D0%B7%D0%BC%D0%B5%D0%BD%D0%B5%D0%BD%D0%B8%D0%B5-%D0%BF%D0%BE%D0%B7%D0%B8%D1%86%D0%B8%D0%B8-%D0%BE%D1%82%D0%B2%D0%B5%D1%82%D0%B0-%D0%B2%D0%BE%D0%BF%D1%80%D0%BE%D1%81%D0%B0][Документация]
	 * 
	 * @param  string  код анкеты
	 * @param  string  код вопроса
	 * @param  array   коды ответов в нужном порядке
	 * 
	 * @return array
	 */
	public function anketa_quest_response_order($anketa, $question, $order)
	{
		$this->params = $this->auth+array(
			'action'    => 'anketa.quest.response.order',
			'anketa.id' => $anketa,
			'id'        => $question,
			'order'     => $order
		);
		
		return $this->send();
	}
	
	/**
	 * Удаляет ответ из вопроса анкеты.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A3%D0%B4%D0%B0%D0%BB%D0%B5%D0%BD%D0%B8%D0%B5-%D0%BE%D1%82%D0%B2%D0%B5%D1%82%D0%B0-%D0%B2%D0%BE%D0%BF%D1%80%D0%BE%D1%81%D0%B0-%D0%B0%D0%BD%D0%BA%D0%B5%D1%82%D1%8B][Документация]
	 * 
	 * @param  string  код анкеты
	 * @param  string  код вопроса
	 * @param  string  код ответа
	 * 
	 * @return array
	 */
	public function anketa_quest_response_delete($anketa, $question, $answer)
	{
		$this->params = $this->auth+array(
			'action'    => 'anketa.quest.response.delete',
			'anketa.id' => $anketa,
			'quest.id'  => $question,
			'id'        => $answer
		);
		
		return $this->send();
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
	 * @param  mixed   код шаблона письма-приветствия (int) или не высылать письмо (NULL)
	 * @param  int     необходимость подтверждения внесения в базу
	 * @param  string  правило изменения ответов анкетных данных (error|update|overwrite)
	 * @param  string  тип адреса подписчика (email|msisdn)
	 * 
	 * @return array
	 */
	public function member_set($email, $data=NULL, $notify=NULL, $confirm=FALSE, $if_exists='overwrite', $addr_type='email')
	{
		$this->params = $this->auth+array(
			'action'         => 'member.set',
			'addr_type'      => $addr_type,
			'email'          => $email,
			'source'         => $_SERVER['REMOTE_ADDR'],
			'if_exists'      => $if_exists,
			'newbie.confirm' => $confirm,
		);
		
		$this->param('obj', $data);
		$this->param('newbie.letter.no-confirm', $notify);

		return $this->send();
	}
	
	/**
	 * Удаляет пользователя из списка рассылки.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A3%D0%B4%D0%B0%D0%BB%D0%B8%D1%82%D1%8C-%D0%BF%D0%BE%D0%B4%D0%BF%D0%B8%D1%81%D1%87%D0%B8%D0%BA%D0%B0][Документация]
	 * 
	 * @param  mixed  список удаляемых емэйлов (array) или код группы (string)
	 * @param  bool   флаг асинхронного запуска
	 * 
	 * @return array
	 */
	public function member_delete($data, $sync=FALSE)
	{
		$this->params = $this->auth+array(
			'action' => 'member.delete',
			'sync'   => $sync
		);

		if (is_array($data))
		{
			$this->params['list'] = $data;
		}
		else
		{
			$this->params['group'] = $data;
		}
		
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
	 * @param  string  формат выпуска
	 * 
	 * @return array
	 */
	public function issue_list($from='1900-01-01', $to=NULL, $groups=array(), $format='email')
	{
		$this->params = $this->auth+array(
			'action' => 'issue.list',
			'from'   => $from,
			'group'  => $groups,
			'format' => $format
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
	 * @param  array   фильтр; может содержать следующие параметры:
	 *                     gid — код группы
	 *                     from — событие произошло начиная с даты (включительно; формат ГГГГ-ММ-ДД)
	 *                     to — событие произошло не позже даты (включительно; формат ГГГГ-ММ-ДД)
	 *                     issue.from — событие произошло из выпуска вышедшего начиная с даты (включительно; формат ГГГГ-ММ-ДД)
	 *                     issue.to — событие произошло из выпуска вышедшего не позже даты (включительно; формат ГГГГ-ММ-ДД)
	 *                 из следующих параметров можно указать только один (исключение: можно совместить with_deliver и with_errs)
	 *                     with_deliver => 1 — включить получивших выпуск
	 *                     with_errs => 1 — включить подписчиков с ошибками доставки
	 *                     with_remove => 1 — включить отписавшихся
	 *                     with_read => 1 — включить прочитавших выпуск
	 *                     with_links => 1 — включить перешедших по ссылкам
	 * @param  mixed   способ возврата результата; тип (response|save) или список получателей (array)
	 * @param  string  формат вывода (csv|xlsx)
	 * @param  int     число строк на странице
	 * @param  int     текущая страница
	 * 
	 * @return array
	 */
	public function stat_activity($filter=array(), $result='save', $format='csv', $limit=20, $page=1)
	{
		$this->params = $this->auth+$filter+array(
			'action'   => 'stat.activity',
			'sort'     => 'date',
			'desc'     => 1,
			'result'   => is_array($result) ? 'email' : $result,
			'page'     => $page,
			'pagesize' => $limit
		);
		
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
	 * Запрашивает статистику по выпускам.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A1%D1%82%D0%B0%D1%82%D0%B8%D1%81%D1%82%D0%B8%D0%BA%D0%B0-%D0%B2%D1%8B%D0%BF%D1%83%D1%81%D0%BA%D0%BE%D0%B2][Документация]
	 * 
	 * @param  string  начиная с даты (формат YYYY-MM-DD)
	 * @param  string  заканчивая датой (формат YYYY-MM-DD)
	 * @param  array   список идентификаторов групп
	 * @param  string  способ группировки по времени
	 * @param  string  итог по всем записям (none — не нужен | yes  — нужен | only — только итог)
	 * @param  bool    вывод статистики по группам подписчиков без единого выпуска
	 * @param  mixed   способ возврата результата; тип (response|save) или список получателей (array)
	 * @param  string  формат вывода (csv|xlsx)
	 * 
	 * @return array
	 */
	public function stat_issue($from=NULL, $to=NULL, $groups=array(), $groupby='YM', $total='none', $withempty=FALSE, $result='save', $format='csv')
	{
		$this->params = $this->auth+array(
			'action'     => 'stat.issue',
			'group'      => $groups,
			'groupby'    => $groupby,
			'total'      => $total,
			'withempty'  => $withempty,
			'result'     => is_array($result) ? 'email' : $result
		);
		
		$this->param('issue.from', $from);
		$this->param('issue.upto', $to);

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
	 * Универсальная функция извлечения статистики.
	 * Позволяет получить информацию про переходы, открытия писем, тиражи выпусков и результаты доставки.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A3%D0%BD%D0%B8%D0%B2%D0%B5%D1%80%D1%81%D0%B0%D0%BB%D1%8C%D0%BD%D0%B0%D1%8F-%D1%81%D1%82%D0%B0%D1%82%D0%B8%D1%81%D1%82%D0%B8%D0%BA%D0%B0][Документация]
	 * 
	 * @param  array   список полей и функций для выборки
	 * @param  array   фильтр результатов
	 * @param  array   сортировка результата
	 * @param  mixed   способ возврата результата; тип (response|save) или список получателей (array)
	 * @param  string  формат вывода (csv|xlsx)
	 * @param  int     число пропускаемых от начала строк данных отчёта
	 * @param  int     число выбираемых строк
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
	 * Создаёт группу.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A1%D0%BE%D0%B7%D0%B4%D0%B0%D1%82%D1%8C-%D0%B3%D1%80%D1%83%D0%BF%D0%BF%D1%83][Документация]
	 * 
	 * @param  string  название группы
	 * @param  string  тип группы (list|filter)
	 * @param  string  код группы
	 * @param  string  тип адресов (email|msisdn)
	 * 
	 * @return array
	 */
	public function group_create($name, $type='list', $id=NULL, $addr_type='email')
	{
		$this->params = $this->auth+array(
			'action'    => 'group.create',
			'name'      => $name,
			'type'      => $type,
			'addr_type' => $addr_type
		);

		$this->param('id', $id);
		
		return $this->send();
	}
	
	/**
	 * Удаляет участников группы-списка.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%9E%D1%87%D0%B8%D1%81%D1%82%D0%B8%D1%82%D1%8C-%D0%B3%D1%80%D1%83%D0%BF%D0%BF%D1%83-%D1%81%D0%BF%D0%B8%D1%81%D0%BE%D0%BA][Документация]
	 * 
	 * @param  string  код группы
	 * @param  mixed   подписчики, которых надо удалить (all | string — емэйл подписчика | array — список емэйлов)
	 * @param  bool    асинхронность запуска
	 * 
	 * @return array
	 */
	public function group_clean($id, $list='all', $sync=FALSE)
	{
		$this->params = $this->auth+array(
			'action' => 'group.clean',
			'id'     => $id,
			'sync'   => $sync
		);

		if ($list === 'all')
		{
			$this->params['all'] = TRUE;
		}
		elseif (is_string($list))
		{
			$this->params['email'] = $list;
		}
		elseif (is_array($list))
		{
			$this->params['list'] = $list;
		}
		
		return $this->send();
	}
	
	/**
	 * Изменяет название группы.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%98%D0%B7%D0%BC%D0%B5%D0%BD%D0%B8%D1%82%D1%8C-%D0%B3%D1%80%D1%83%D0%BF%D0%BF%D1%83][Документация]
	 * 
	 * @param  string  код группы
	 * @param  string  название группы
	 * 
	 * @return array
	 */
	public function group_set($id, $name)
	{
		$this->params = $this->auth+array(
			'action' => 'group.set',
			'id'     => $id,
			'name'   => $name
		);
		
		return $this->send();
	}
	
	/**
	 * Считывает группу.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%9F%D1%80%D0%BE%D1%87%D0%B8%D1%82%D0%B0%D1%82%D1%8C-%D0%B3%D1%80%D1%83%D0%BF%D0%BF%D1%83][Документация]
	 * 
	 * @param  mixed  код группы (string) или список групп (array)
	 * @param  bool   возвращать фильтр группы
	 * 
	 * @return array
	 */
	public function group_get($id, $filter=FALSE)
	{
		$this->params = $this->auth+array(
			'action'      => 'group.get',
			'id'          => $id,
			'with_filter' => $filter
		);
		
		return $this->send();
	}
	
	/**
	 * Создаёт копию подписчиков группы.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A1%D0%BD%D0%B8%D0%BC%D0%BE%D0%BA-%D0%B3%D1%80%D1%83%D0%BF%D0%BF%D1%8B-%D0%A0%D0%B0%D1%81%D1%88%D0%B8%D1%80%D0%B8%D1%82%D1%8C-%D0%B3%D1%80%D1%83%D0%BF%D0%BF%D1%83-%D1%81%D0%BF%D0%B8%D1%81%D0%BE%D0%BA][Документация]
	 * 
	 * @param  mixed   код группы (string) или список подписчиков (array)
	 * @param  string  код группы
	 * @param  bool    очистить группу перед внесением
	 * @param  bool    асинхронность вызова
	 * 
	 * @return array
	 */
	public function group_snapshot($from, $to, $clean=FALSE, $sync=FALSE)
	{
		$this->params = $this->auth+array(
			'action' => 'group.snapshot',
			'to'     => array('id' => $to, 'clean' => $clean),
			'from'   => array('sync' => $sync)
		);

		if (is_string($from))
		{
			$this->params['from']['group'] = $from;
		}
		elseif (is_array($from))
		{
			$this->params['from']['list'] = $from;
		}
		
		return $this->send();
	}
	
	/**
	 * Возвращает правила фильтрации группы.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%9F%D0%BE%D0%BB%D1%83%D1%87%D0%B8%D1%82%D1%8C-%D0%BF%D1%80%D0%B0%D0%B2%D0%B8%D0%BB%D0%B0-%D1%84%D0%B8%D0%BB%D1%8C%D1%82%D1%80%D0%B0%D1%86%D0%B8%D0%B8-%D0%B3%D1%80%D1%83%D0%BF%D0%BF%D1%8B][Документация]
	 * 
	 * @param  string  код группы
	 * 
	 * @return array
	 */
	public function group_filter_get($id)
	{
		$this->params = $this->auth+array(
			'action' => 'group.filter.get',
			'id'     => $id
		);

		return $this->send();
	}
	
	/**
	 * Изменяет правила фильтрации группы.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A3%D1%81%D1%82%D0%B0%D0%BD%D0%BE%D0%B2%D0%B8%D1%82%D1%8C-%D0%BF%D1%80%D0%B0%D0%B2%D0%B8%D0%BB%D0%B0-%D1%84%D0%B8%D0%BB%D1%8C%D1%82%D1%80%D0%B0%D1%86%D0%B8%D0%B8-%D0%B3%D1%80%D1%83%D0%BF%D0%BF%D1%8B][Документация]
	 * 
	 * @param  string  код группы
	 * @param  array   правила фильтрации
	 * 
	 * @return array
	 */
	public function group_filter_set($id, $filter)
	{
		$this->params = $this->auth+array(
			'action' => 'group.filter.set',
			'id'     => $id,
			'filter' => $filter
		);

		return $this->send();
	}
	
	/**
	 * Удаляет группу.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A3%D0%B4%D0%B0%D0%BB%D0%B8%D1%82%D1%8C-%D0%B3%D1%80%D1%83%D0%BF%D0%BF%D1%83][Документация]
	 * 
	 * @param  string  код группы
	 * 
	 * @return array
	 */
	public function group_delete($id)
	{
		$this->params = $this->auth+array(
			'action' => 'group.delete',
			'id'     => $id
		);

		return $this->send();
	}
	
	/**
	 * Возвращает общую статистику по группе.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%9E%D0%B1%D1%89%D0%B0%D1%8F-%D1%81%D1%82%D0%B0%D1%82%D0%B8%D1%81%D1%82%D0%B8%D0%BA%D0%B0-%D0%BF%D0%BE-%D0%B3%D1%80%D1%83%D0%BF%D0%BF%D0%B5][Документация]
	 * 
	 * @param  array   коды групп; если пусто - по всем
	 * @param  mixed   способ возврата результата; тип (response|save) или список получателей (array)
	 * @param  string  формат вывода (csv|xlsx)
	 * 
	 * @return array
	 */
	public function stat_group_common($groups=array(), $result='save', $format='csv')
	{
		$this->params = $this->auth+array(
			'action' => 'stat.group.common',
			'group'  => $groups,
			'result' => is_array($result) ? 'email' : $result
		);
		
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
	 * Импотирует список подписчиков.
	 * В случае указания ссылки на список подписчиков, файл должен быть в UTF-8, а поля разделяться запятыми (CSV-формат).
	 * Первой строкой или элементом массива идёт заголовок.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%92%D0%BD%D0%B5%D1%81%D0%B5%D0%BD%D0%B8%D0%B5-%D1%81%D0%BF%D0%B8%D1%81%D0%BA%D0%B0-%D0%BF%D0%BE%D0%B4%D0%BF%D0%B8%D1%81%D1%87%D0%B8%D0%BA%D0%BE%D0%B2][Документация]
	 * 
	 * @param  mixed   список подписчиков
	 *                     string — ссылка на файл с подписчиками;
	 *                     integer — идентификатор уже загруженных данных;
	 *                     array — массив подписчиков
	 * @param  array   группа импорта подписчиков
	 * @param  string  действие если адрес существует (overwrite|ignore|error)
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
			'sequence.event' => $trigger,
			'newbie.confirm' => $confirm
		);
		

		if (is_string($data))
		{
			$this->params['users.url'] = $data;
		}
		elseif (is_numeric($data))
		{
			$this->params['uid'] = $data;
		}
		elseif (is_array($data))
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
	 * @param  array  параметры черновика
	 *                    name — название черновика
	 *                    format — формат черновика (html|sms|text)
	 *                    division — идентификатор подразделения, имеющего доступ к черновику
	 *                    from — емэйл отправителя
	 *                    sender — имя отправителя
	 *                    reply.email — обратный адрес для ответа
	 *                    reply.name — имя для обратного адреса для ответа
	 *                    to.name — имя получателя
	 *                    subject — тема письма
	 *                    text — содердимое черновика
	 * @param  int    код черновика
	 * 
	 * @return array
	 */
	public function issue_draft_set($params, $id=NULL)
	{
		$this->params = $this->auth+array(
			'action'           => 'issue.draft.set',
			'obj'              => $params,
			'return_fresh_obj' => TRUE
		);

		$this->param('id', $id);
		
		return $this->send();
	}

	/**
	 * Удаляет черновик.
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
				'draft.id'   => is_numeric($from) ? $from : NULL,
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
	 * Возвращает список последовательностей.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A1%D0%BF%D0%B8%D1%81%D0%BE%D0%BA-%D0%BF%D0%BE%D1%81%D0%BB%D0%B5%D0%B4%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D1%8C%D0%BD%D0%BEc%D1%82%D0%B5%D0%B9][Документация]
	 * 
	 * @return array
	 */
	public function sequence_list()
	{
		$this->params = $this->auth+array(
			'action' => 'sequence.list'
		);
		
		return $this->send();
	}

	/**
	 * Создаёт последовательность.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#C%D0%BE%D0%B7%D0%B4%D0%B0%D1%82%D1%8C-%D0%BF%D0%BE%D1%81%D0%BB%D0%B5%D0%B4%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D1%8C%D0%BD%D0%BE%D1%81%D1%82%D1%8C][Документация]
	 * 
	 * @param  string  название последовательности
	 * @param  bool    однократность последовательности
	 * @param  bool    закрытость для новых участников
	 * @param  bool    возобновлять прохождение при увеличении количества шагов
	 * @param  bool    отстановка последовательности
	 * 
	 * @return array
	 */
	public function sequence_create($name, $onlyonce=FALSE, $closed=FALSE, $rog=FALSE, $pause=FALSE)
	{
		$this->params = $this->auth+array(
			'action'            => 'sequence.create',
			'name'              => $name,
			'onlyonce'          => $onlyonce,
			'parrallel'         => 0,
			'closed'            => $closed,
			'resume_on_growing' => $rog,
			'pause'             => $pause
		);
		
		return $this->send();
	}

	/**
	 * Возвращает параметры последовательности.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%9F%D1%80%D0%BE%D1%87%D0%B8%D1%82%D0%B0%D1%82%D1%8C-%D0%BF%D0%BE%D1%81%D0%BB%D0%B5%D0%B4%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D1%8C%D0%BD%D0%BE%D1%81%D1%82%D1%8C][Документация]
	 * 
	 * @param  int  код последовательности
	 * 
	 * @return array
	 */
	public function sequence_get($id)
	{
		$this->params = $this->auth+array(
			'action' => 'sequence.get',
			'id'     => $id
		);
		
		return $this->send();
	}

	/**
	 * Изменяет параметры последовательности.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%98%D0%B7%D0%BC%D0%B5%D0%BD%D0%B8%D1%82%D1%8C-%D0%BF%D0%BE%D1%81%D0%BB%D0%B5%D0%B4%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D1%8C%D0%BD%D0%BE%D1%81%D1%82%D1%8C][Документация]
	 * 
	 * @param  int     код последовательности
	 * @param  string  название последовательности
	 * @param  bool    однократность последовательности
	 * @param  bool    закрытость для новых участников
	 * @param  bool    возобновлять прохождение при увеличении количества шагов
	 * @param  bool    отстановка последовательности
	 * 
	 * @return array
	 */
	public function sequence_set($id, $name=NULL, $onlyonce=NULL, $closed=NULL, $rog=NULL, $pause=NULL)
	{
		$this->params = $this->auth+array(
			'action' => 'sequence.set',
			'id'     => $id
		);

		$this->param('name', $name);
		$this->param('pause', $pause);
		$this->param('closed', $closed);
		$this->param('onlyonce', $onlyonce);
		$this->param('resume_on_growing', $rog);
		
		return $this->send();
	}

	/**
	 * Удаляет последовательность.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A3%D0%B4%D0%B0%D0%BB%D0%B8%D1%82%D1%8C-%D0%BF%D0%BE%D1%81%D0%BB%D0%B5%D0%B4%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D1%8C%D0%BD%D0%BE%D1%81%D1%82%D1%8C][Документация]
	 * 
	 * @param  int  код последовательности
	 * 
	 * @return array
	 */
	public function sequence_delete($id)
	{
		$this->params = $this->auth+array(
			'action' => 'sequence.delete',
			'id'     => $id
		);
		
		return $this->send();
	}

	/**
	 * Получает список шагов последовательности.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%9F%D0%BE%D0%BB%D1%83%D1%87%D0%B8%D1%82%D1%8C-%D1%81%D0%BF%D0%B8%D1%81%D0%BE%D0%BA-%D1%88%D0%B0%D0%B3%D0%BE%D0%B2][Документация]
	 * 
	 * @param  int  код последовательности
	 * 
	 * @return array
	 */
	public function sequence_steps_get($id)
	{
		$this->params = $this->auth+array(
			'action' => 'sequence.steps.get',
			'id'     => $id
		);

		return $this->send();
	}

	/**
	 * Задаёт шаги последовательности.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A3%D1%81%D1%82%D0%B0%D0%BD%D0%BE%D0%B2%D0%B8%D1%82%D1%8C-%D1%81%D0%BF%D0%B8%D1%81%D0%BE%D0%BA-%D1%88%D0%B0%D0%B3%D0%BE%D0%B2][Документация]
	 * 
	 * @param  int    код последовательности
	 * @param  array  шаги последовательности
	 * 
	 * @return array
	 */
	public function sequence_steps_set($id, $steps)
	{
		$this->params = $this->auth+array(
			'action' => 'sequence.steps.set',
			'id'     => $id,
			'list'   => $steps
		);

		return $this->send();
	}

	/**
	 * Запрашивает статистику последовательности.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A1%D1%82%D0%B0%D1%82%D0%B8%D1%81%D1%82%D0%B8%D0%BA%D0%B0-%D0%BF%D0%BE%D1%81%D0%BB%D0%B5%D0%B4%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D1%8C%D0%BD%D0%BE%D1%81%D1%82%D0%B8][Документация]
	 * 
	 * @param  int  код последовательности
	 * 
	 * @return array
	 */
	public function sequence_stats($id)
	{
		$this->params = $this->auth+array(
			'action' => 'sequence.stats',
			'id'     => $id
		);

		return $this->send();
	}

	/**
	 * Возвращает список участников последовательности.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A1%D0%BF%D0%B8%D1%81%D0%BE%D0%BA-%D1%83%D1%87%D0%B0%D1%81%D1%82%D0%BD%D0%B8%D0%BA%D0%BE%D0%B2-%D0%BF%D0%BE%D1%81%D0%BB%D0%B5%D0%B4%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D1%8C%D0%BD%D0%BE%D1%81%D1%82%D0%B8][Документация]
	 * 
	 * @param  int     код последовательности
	 * @param  string  способ группировки (member|step) или не группировать (NULL)
	 * @param  array   список интересующих шагов
	 * 
	 * @return array
	 */
	public function sequence_member_list($id, $group=NULL, $steps=NULL)
	{
		$this->params = $this->auth+array(
			'action' => 'sequence.member.list',
			'id'     => $id
		);

		$this->param('groupby', $group);
		$this->param('steps', $steps);

		return $this->send();
	}

	/**
	 * Отправляет подписчика на последовательность.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%9D%D0%B0%D1%87%D0%B0%D1%82%D1%8C-%D0%BF%D1%80%D0%BE%D1%85%D0%BE%D0%B6%D0%B4%D0%B5%D0%BD%D0%B8%D0%B5-%D0%BF%D0%BE%D1%81%D0%BB%D0%B5%D0%B4%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D1%8C%D0%BD%D0%BE%D1%81%D1%82%D0%B8][Документация]
	 * 
	 * @param  int    код последовательности
	 * @param  mixed  список емэйлов (array) или код группы (string)
	 * 
	 * @return array
	 */
	public function sequence_member_start($id, $users)
	{
		$this->params = $this->auth+array(
			'action' => 'sequence.member.start',
			'id'     => $id
		);

		if (is_array($users))
		{
			$this->param('list', $users);
		}
		else
		{
			$this->param('group', $users);
		}

		return $this->send();
	}

	/**
	 * Приостанавливает прохождение подписчиком последовательности.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%9F%D1%80%D0%B8%D0%BE%D1%81%D1%82%D0%B0%D0%BD%D0%BE%D0%B2%D0%B8%D1%82%D1%8C-%D0%BF%D1%80%D0%BE%D1%85%D0%BE%D0%B6%D0%B4%D0%B5%D0%BD%D0%B8%D0%B5-%D0%BF%D0%BE%D1%81%D0%BB%D0%B5%D0%B4%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D1%8C%D0%BD%D0%BE%D1%81%D1%82%D0%B8][Документация]
	 * 
	 * @param  int    код последовательности
	 * @param  mixed  список емэйлов (array) или код группы (string)
	 * 
	 * @return array
	 */
	public function sequence_member_pause($id, $users)
	{
		$this->params = $this->auth+array(
			'action' => 'sequence.member.pause',
			'id'     => $id
		);

		if (is_array($users))
		{
			$this->param('list', $users);
		}
		else
		{
			$this->param('group', $users);
		}

		return $this->send();
	}

	/**
	 * Возобновляет прохождение подписчиком последовательности.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%92%D0%BE%D0%B7%D0%BE%D0%B1%D0%BD%D0%BE%D0%B2%D0%B8%D1%82%D1%8C-%D0%BF%D1%80%D0%BE%D1%85%D0%BE%D0%B6%D0%B4%D0%B5%D0%BD%D0%B8%D0%B5-%D0%BF%D0%BE%D1%81%D0%BB%D0%B5%D0%B4%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D1%8C%D0%BD%D0%BE%D1%81%D1%82%D0%B8][Документация]
	 * 
	 * @param  int    код последовательности
	 * @param  mixed  список емэйлов (array) или код группы (string)
	 * 
	 * @return array
	 */
	public function sequence_member_resume($id, $users)
	{
		$this->params = $this->auth+array(
			'action' => 'sequence.member.resume',
			'id'     => $id
		);

		if (is_array($users))
		{
			$this->param('list', $users);
		}
		else
		{
			$this->param('group', $users);
		}

		return $this->send();
	}

	/**
	 * Завершает прохождение подписчиками последовательности.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%9F%D1%80%D0%B5%D1%80%D0%B2%D0%B0%D1%82%D1%8C-%D0%BF%D1%80%D0%BE%D1%85%D0%BE%D0%B6%D0%B4%D0%B5%D0%BD%D0%B8%D0%B5-%D0%BF%D0%BE%D1%81%D0%BB%D0%B5%D0%B4%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D1%8C%D0%BD%D0%BE%D1%81%D1%82%D0%B8][Документация]
	 * 
	 * @param  int    код последовательности
	 * @param  mixed  список емэйлов (array) или код группы (string)
	 * 
	 * @return array
	 */
	public function sequence_member_stop($id, $users)
	{
		$this->params = $this->auth+array(
			'action' => 'sequence.member.stop',
			'id'     => $id
		);

		if (is_array($users))
		{
			$this->param('list', $users);
		}
		else
		{
			$this->param('group', $users);
		}
		
		return $this->send();
	}

	/**
	 * Возвращает список последовательностей, где числится указанный подписчик.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A3%D1%87%D0%B0%D1%81%D1%82%D0%B8%D0%B5-%D0%BF%D0%BE%D0%BB%D1%8C%D0%B7%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D1%8F-%D0%B2-%D0%BF%D0%BE%D1%81%D0%BB%D0%B5%D0%B4%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D1%8C%D0%BD%D0%BE%D1%81%D1%82%D1%8F%D1%85][Документация]
	 * 
	 * @param  string  емэйл подписчика
	 * @param  mixed   код последовательности (int)
	 * 
	 * @return array
	 */
	public function sequence_member_membership($email, $id=NULL)
	{
		$this->params = $this->auth+array(
			'action' => 'sequence.member.membership',
			'email'  => $email
		);

		$this->param('id', $id);
		
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
			'status'       => $status
		);

		$this->param('email', $email);
		$this->param('password.old', $old_password);
		$this->param('password.new', $new_password);
		
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
	 * @param  datetime  дата события от (формат ГГГГ-ММ-ДД ЧЧ:ММ:СС)
	 * @param  datetime  дата события по (формат ГГГГ-ММ-ДД ЧЧ:ММ:СС)
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
	 * Запрашивает права доступа пользователя.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A7%D1%82%D0%B5%D0%BD%D0%B8%D0%B5-%D0%BF%D1%80%D0%B0%D0%B2][Документация]
	 *
	 * @param  string  логин пользователя
	 *
	 * @return array
	 */
	public function rights_get($login)
	{
		$this->params = $this->auth+array(
			'action' => 'rights.get',
			'user'   => $login
		);
		
		return $this->send();
	}

	/**
	 * Уставнавливает права доступа пользователя.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A3%D1%81%D1%82%D0%B0%D0%BD%D0%BE%D0%B2%D0%BA%D0%B0-%D0%BF%D1%80%D0%B0%D0%B2][Документация]
	 *
	 * @param  string  логин пользователя
	 * @param  array   список устанавливаемых прав
	 *
	 * @return array
	 */
	public function rights_set($login, $rights)
	{
		$this->params = $this->auth+array(
			'action' => 'rights.set',
			'user'   => $login,
			'list'   => $rights
		);
		
		return $this->send();
	}

	/**
	 * Возвращает список внешних авторизаций.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A1%D0%BF%D0%B8%D1%81%D0%BE%D0%BA-%D0%B2%D0%BD%D0%B5%D1%88%D0%BD%D0%B8%D1%85-%D0%B0%D0%B2%D1%82%D0%BE%D1%80%D0%B8%D0%B7%D0%B0%D1%86%D0%B8%D0%B9][Документация]
	 *
	 * @return array
	 */
	public function authext_list()
	{
		$this->params = $this->auth+array(
			'action' => 'authext.list'
		);
		
		return $this->send();
	}

	/**
	 * Считывает параметры внешней авторизации.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A7%D1%82%D0%B5%D0%BD%D0%B8%D0%B5-%D0%B2%D0%BD%D0%B5%D1%88%D0%BD%D0%B5%D0%B9-%D0%B0%D0%B2%D1%82%D0%BE%D1%80%D0%B8%D0%B7%D0%B0%D1%86%D0%B8%D0%B8][Документация]
	 * 
	 * @param  string  код внешней авторизации
	 *
	 * @return array
	 */
	public function authext_get($id)
	{
		$this->params = $this->auth+array(
			'action' => 'authext.get',
			'id'     => $id
		);
		
		return $this->send();
	}

	/**
	 * Создаёт внешнюю авторизацию.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A1%D0%BE%D0%B7%D0%B4%D0%B0%D0%BD%D0%B8%D0%B5-%D0%B2%D0%BD%D0%B5%D1%88%D0%BD%D0%B5%D0%B9-%D0%B0%D0%B2%D1%82%D0%BE%D1%80%D0%B8%D0%B7%D0%B0%D1%86%D0%B8%D0%B8][Документация]
	 * 
	 * @param  string  логин внешней авторизации
	 * @param  string  токен внешней авторизации (refresh token)
	 *
	 * @return array
	 */
	public function authext_create($login, $token)
	{
		$this->params = $this->auth+array(
			'action' => 'authext.create',
			'type'   => 8, // Google Analytics
			'login'  => $login,
			'token'  => $token
		);
		
		return $this->send();
	}

	/**
	 * Изменяет внешнюю авторизацию.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%98%D0%B7%D0%BC%D0%B5%D0%BD%D0%B5%D0%BD%D0%B8%D0%B5-%D0%B2%D0%BD%D0%B5%D1%88%D0%BD%D0%B5%D0%B9-%D0%B0%D0%B2%D1%82%D0%BE%D1%80%D0%B8%D0%B7%D0%B0%D1%86%D0%B8%D0%B8][Документация]
	 * 
	 * @param  string  код внешней авторизации
	 * @param  string  логин внешней авторизации
	 * @param  string  токен внешней авторизации (refresh token)
	 *
	 * @return array
	 */
	public function authext_set($id, $login=NULL, $token=NULL)
	{
		$this->params = $this->auth+array(
			'action' => 'authext.set',
			'id'     => $id,
			'type'   => 8 // Google Analytics
		);

		$this->param('login', $login);
		$this->param('token', $token);
		
		return $this->send();
	}

	/**
	 * Удаляет внешнюю авторизацию.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%A3%D0%B4%D0%B0%D0%BB%D0%B5%D0%BD%D0%B8%D0%B5-%D0%B2%D0%BD%D0%B5%D1%88%D0%BD%D0%B5%D0%B9-%D0%B0%D0%B2%D1%82%D0%BE%D1%80%D0%B8%D0%B7%D0%B0%D1%86%D0%B8%D0%B8][Документация]
	 * 
	 * @param  string  код внешней авторизации
	 *
	 * @return array
	 */
	public function authext_delete($id)
	{
		$this->params = $this->auth+array(
			'action' => 'authext.delete',
			'id'     => $id
		);
		
		return $this->send();
	}

	/**
	 * Возвращает информацию об авторизации в Google Analytics.
	 * 
	 * @link  [https://pro.subscribe.ru/API/API.html#%D0%98%D0%BD%D1%84%D0%BE%D1%80%D0%BC%D0%B0%D1%86%D0%B8%D1%8F-%D0%BE%D0%B1-%D0%B0%D0%B2%D1%82%D0%BE%D1%80%D0%B8%D0%B7%D0%B0%D1%86%D0%B8%D0%B8-%D0%B2-Google-Analitics][Документация]
	 * 
	 * @param  string  код внешней авторизации
	 *
	 * @return array
	 */
	public function authext_ga_props($id)
	{
		$this->params = $this->auth+array(
			'action' => 'authext.ga.props',
			'id'     => $id
		);
		
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
		$outOfQuotes = TRUE;
	
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

				for ($j = 0; $j < $pos; $j++)
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


