<?php
namespace BladeBTC\Commands;
use BladeBTC\Helpers\Btc;
use BladeBTC\Models\BotSetting;
use BladeBTC\Models\Users;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
class ImpoCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "impo";
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
<b>Como funciona?</b>\n
CONSEJOS PARA GUARDAR TU SEED  O CONTRASEÑA SEMILLA  O RESPALDAR TUS FONDOS💎

Mantener su semilla de recuperación segura fuera de la vista. Puede guardarlo en una caja fuerte en su propia casa, o puede encontrar una alternativa segura lejos de su propiedad.

Aunque mantener su semilla de recuperación fuera del sitio puede ser inconveniente cuando llega el momento de actualizar el firmware más reciente, la seguridad y la conveniencia a menudo están inversamente relacionadas.


Para proteger su tarjeta de semillas de recuperación mientras está en su caja fuerte, laminarla. Los laminadores son bastante económicos, y mantendrán su tarjeta a salvo de daños accidentales por agua u otras manchas y daños físicos como rasgaduras o manchas.

 No recomendamos llevar su tarjeta a una tienda para laminarla. Eres la única persona que debería ver tu semilla de recuperación.

También puede almacenar más de una copia de su semilla de recuperación. Recibes dos cartas en cada caja de Trezor.

Deben almacenarse por separado para aumentar su propia seguridad y disminuir la posibilidad de perder su semilla de recuperación por accidente o robo. Una copia puede ir en su caja fuerte en casa, y la otra puede almacenarse en un banco, por ejemplo.

Puede mantener su semilla de recuperación a salvo de daños físicos grabándola en una placa de acero. Nuevamente, las herramientas de grabado son bastante económicas y vale la pena la inversión si tiene más de una semilla de recuperación para proteger.

Como consejo final, incluya el acceso a su semilla de recuperación en su testamento. Desea sentirse lo más seguro posible, pero no olvide planificar para cualquier eventualidad.

Puede leer online sobre los casos múltiples de personas que murieron y no dejaron su semilla de acceso o semilla de recuperación al alcance de su familia.

CR COIN CR.
\n<b>Support Chat</b>\n" . BotSetting::getValueByName("support_chat_id"),
                'reply_markup' => $reply_markup,
                'parse_mode' => 'HTML',
            ]);
        }
    }
}
