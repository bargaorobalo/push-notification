<?php

namespace PushNotification\Model;

/**
 * Códigos de status http
 */
class HttpStatusCode {
	/**
	 * A requisição foi processada com sucesso
	 *
	 * @var int
	 */
	const OK = 200;

	/**
	 * A requisição foi processada com sucesso e o resource foi criado com sucesso
	 *
	 * @var int
	 */
	const CREATED = 201;

	/**
	 * A requisição foi processada com sucesso, nenhum dado a retornar
	 *
	 * @var int
	 */
	const NO_CONTENT = 204;

	/**
	 * A requisição contém dados inválidos ou nem todos os dados necessários foram informados
	 *
	 * @var int
	 */
	const BAD_REQUEST = 400;

	/**
	 * A requisição contém um resource que não foi encontrado
	 * @var unknown
	 */
	const NOT_FOUND = 404;

	/**
	 * A requisição contém um conflito com os recursos já existentes
	 */
	const CONFLICT = 409;

	/**
	 * Ocorreu um erro no servidor ao processar a requisição
	 *
	 * @var int
	 */
	const INTERNAL_SERVER_ERROR = 500;
}