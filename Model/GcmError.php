<?php
namespace Unisuam\Model;

/**
 * Códigos de erro do Google Cloud Messaging
 *
 * Gerencia o envio de mensagens via push
 */
class GcmError
{
	/**
	 * Notificação não continha um registration id.
	 */
	const MISSING_REGISTRATION = "MissingRegistration";
	/**
	 * O registration id informado é inválido.
	 */
	const INVALID_REGISTRATION = "InvalidRegistration";

	/**
	 * Servidor não tem permissão para enviar notificações para a aplicacão,
	 * ocorre quando o identificador de quem envia as notificações for alterado .
	 */
	const MISMATCH_SENDER_ID = "MismatchSenderId";

	/**
	 * Indica que o registration id não está registrado, ocorre quando o
	 * dispositivo for manualmente ou automaticamente desregistrado, se expirar,
	 * ou se a aplicação for atualizada e a nova versão não tem um receiver configurado.
	 */
	const NOT_REGISTERED = "NotRegistered";

	/**
	 * O tamanho total dos dados excede o limite máximo de 4096 bytes.
	 */
	const MESSAGE_TOO_BIG = "MessageTooBig";

	/**
	 * Alguma chave para acesso a dados contém uma palavra-chave utilizada pelo google
	 * e não pode ser usada.
	 */
	const INVALID_DATA_KEY = "InvalidDataKey";

	/**
	 * Tempo de vida da mensagem é inválido
	 */
	const INVALID_TIME_TO_LIVE = "InvalidTtl";

	/**
	 * Erro interno do serviço do Google Cloud Messaging
	 */
	const INTERNAL_SERVER_ERROR = "InternalServerError";

	/**
	 * O nome do pacote da aplicação de destino não está de acordo com o registration id informado.
	 */
	const INVALID_PACKAGE_NAME = "InvalidPackageName";
}