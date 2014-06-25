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
	private $secret = null;

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
	 * Segredo que permite o acesso a API
	 *
	 * @return string
	 */
	public function getSecret() {
		return $this->secret;
	}

	/**
	 * Define o segredo que permite o acesso a API
	 *
	 * @param string $token
	 */
	public function setSecret($secret) {
		$this->secret = $secret;
	}
}