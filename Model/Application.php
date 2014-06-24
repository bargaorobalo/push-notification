<?php

namespace PushNotification\Model;

/**
 * Aplicação
 *
 * @Entity
 * @Table(name="applications")
 */
class Application {

	/**
	 * @Id
	 * @Column(type="integer")
	 * @GeneratedValue(strategy="IDENTITY")
	 */
	private $id = null;

	/**
	 * @Column(type="text")
	 */
	private $name = null;

	/**
	 * @Column(type="text")
	 */
	private $token = null;

	/**
	 * Identificador da aplicação
	 *
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Define o identificador da aplicação
	 *
	 * @param int $id
	 */
	public function setId($id) {
		$this->id = $id;
	}

	/**
	 * Nome da aplicação
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Define o nome da aplicação
	 *
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * Token de acesso a API
	 *
	 * @return string
	 */
	public function getToken() {
		return $this->token;
	}

	/**
	 * Define o token de acesso a API
	 *
	 * @param string $token
	 */
	public function setToken($token) {
		$this->token = $token;
	}
}