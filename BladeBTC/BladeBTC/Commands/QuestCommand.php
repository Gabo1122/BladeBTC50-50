<?php
namespace BladeBTC\Commands;
use BladeBTC\Helpers\Btc;
use BladeBTC\Models\BotSetting;
use BladeBTC\Models\Users;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
class QuestCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "quest";
    /**
     * @var string Command Description
     */
    protected $description = "Info menu.";
    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        /**
         * Chat data
         */
        $id = $this->update->getMessage()->getFrom()->getId();
        /**
         * Display Typing...
         */
        $this->replyWithChatAction([ 'action' => Actions::TYPING ]);
        /**
         * Verify user
         */
        $user = new Users($id);
        if ($user->exist() == false) {
            $this->triggerCommand('start');
        }
        else {
            /**
             * Keyboard
             */
             $keyboard = [
         			[ "Balance " . Btc::Format($user->getBalance()) . " \xF0\x9F\x92\xB0" ],
         			[ "Invertir \xF0\x9F\x92\xB5", "Retirar \xE2\x8C\x9B" ],
         			[ "Preguntas \xE2\x86\xA9", "Ayuda \xE2\x9D\x93" ],
         			[ "Mis referidos \xF0\x9F\x91\xAB","Importante \xE2\x80\xBC" ],
         			[ "Idioma-Language \xF0\x9F\x94\xA0" ],
         		];
            $reply_markup = $this->telegram->replyKeyboardMarkup([
                'keyboard' => $keyboard,
                'resize_keyboard' => true,
                'one_time_keyboard' => false,
            ]);
            /**
             * Response
             */
            $this->replyWithMessage([
                'text' => "
<b>¿Cuánto necesito invertir?</b>\n
El equivalente de $ 100 / mes en BTC es la inversión necesaria para mantener un 'estado' de inversionista activo.\n
<b>¿Cómo funciona el bot?</b>\n
Este bot fue creado para ayudar a acumular la criptomoneda 'Bitcoin', sin la necesidad de extraerla o comprarla. Simplemente comparta su enlace personal generado por este bot con sus amigos o familiares. Por cada usuario que se active (invierta $ 100 en BTC), se le volverá a asignar $ 50 por referirlos al bot con su enlace.\n
<b>¿Cómo / cuándo me pagarán?</b>\n
DEBE TENER UN 'ESTADO DE INVERSOR ACTIVO' PARA SER ELEGIBLE PARA PAGOS. El pago por cada referencia es después del lapso de 33 días. Todos los pagos están en Bitcoin, para recibir su pago, use el comando / dirección (ingrese la dirección de la billetera)\n
<b>¿Pórque invertir en éste Bot?</b>\n
¡BlockCRbot le permite a cualquiera comenzar a generar y acumular ingresos en Bitcoin, la criptomoneda más poderosa que existe!
<b>La inversión es en Bitcoin. ¿Pórque?</b>\n
Bitcoin es un activo extremadamente volátil y cuando está en una tendencia alcista, su inversión puede duplicarse, triplicarse o más en solo meses! Bitcoin también es una forma efectiva de realizar depósitos y retiros instantáneos sin la necesidad de un banco o corporación.\n
\n
<b>¿Cómo puedo comprar Bitcoin?</b>\n
Puedes buscar en google maps donde puedes encontrar cajero de Bitcoin, tambien puedes utilizar el servicio <a href='https://old.changelly.com/?ref_id=c9fa0894f875'>Changelly</a> donde puedes comprar BTC con tu tajeta de crédito o débito.\n
\n
\n<b>Si necesitas ayuda contácta a quien te ha referido</b>\n" . BotSetting::getValueByName("support_chat_id"),
                'reply_markup' => $reply_markup,
                'parse_mode' => 'HTML',
            ]);
        }
    }
}
