<?php

declare(strict_types=1);

namespace App\Context\ChadGPT\Infrastructure\Handlers;

use App\Context\Common\Infrastructure\CommandHandlerInterface;
use App\Context\Common\Infrastructure\CommandInterface;
use App\Context\ChadGPT\Domain\Command\CreateChatConversationCommand;
use App\Context\ChadGPT\Domain\Model\ChadGptConversation;
use App\Context\ChadGPT\Domain\Model\ChadGptConversationWordStat;
use Illuminate\Support\Facades\Auth;

class CreateChatConversationHandler implements CommandHandlerInterface
{
    /**
     * @param CreateChatConversationCommand $command
     */
    public function handle(CommandInterface $command): mixed
    {
        ChadGptConversation::create([
            'user_id' => $command->getUser()->getId(),
            'model' => $command->getModel(),
            'user_message' => $command->getUserMessage(),
            'ai_response' => $command->getResponse(),
            'used_tokens_count' => $command->getUserTokensCount()
        ]);

        $statDate = now()->startOfMonth();
        $wordStat = ChadGptConversationWordStat::firstOrCreate([
            'user_id' => Auth::id(),
            'stat_date' => $statDate
        ]);

        $wordStat->tokens_used += $command->getUserTokensCount();
        $wordStat->save();

        return null;
    }

}
