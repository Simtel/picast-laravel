<?php

declare(strict_types=1);

namespace App\Context\ChadGPT\Infrastructure\Handlers;

use App\Common\CommandHandlerInterface;
use App\Common\CommandInterface;
use App\Context\ChadGPT\Domain\Command\CreateChatConversationCommand;
use App\Context\ChadGPT\Domain\Model\ChadGptConversation;
use App\Context\ChadGPT\Domain\Model\ChadGptConversationWordStat;
use Illuminate\Support\Facades\Auth;

class CreateChatConversationHandler implements CommandHandlerInterface
{
    /**
     * @param CreateChatConversationCommand $command
     * @return void
     */
    public function handle(CommandInterface $command): void
    {
        ChadGptConversation::create([
            'user_id' => $command->getUser()->getId(),
            'model' => $command->getModel(),
            'user_message' => $command->getUserMessage(),
            'ai_response' => $command->getResponse(),
            'used_words_count' => $command->getUserWordsCount()
        ]);

        $statDate = now()->startOfMonth();
        $wordStat = ChadGptConversationWordStat::firstOrCreate([
            'user_id' => Auth::id(),
            'stat_date' => $statDate
        ]);

        $wordStat->words_used += $command->getUserWordsCount();
        $wordStat->save();
    }

}
