<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\MsgException;
use App\Http\JsonResponse;
use App\Http\Requests\MessageRequest;
use App\Models\Faq;
use App\Repositories\FaqRepository;
use App\Repositories\MessageRepository;
use Illuminate\Database\QueryException;

class MessageController extends BaseController
{
    public function index(){
        $faqs = app(FaqRepository::class)->getByUri('message');
        return template('message', ['faqs' => $faqs]);
    }

    /**
     * 添加留言
     * @param MessageRequest $request
     * @param MessageRepository $messageRepository
     * @return false|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|string
     */
    public function store(MessageRequest $request,MessageRepository $messageRepository){
        try {
            if($messageRepository->groupByDayIpCount() >= 5){
                throw new MsgException("留言次數過多，請稍後再試");
            }
            $messageRepository->store($request->all());
            return JsonResponse::make()->title('留言成功')->message('我們會儘快回復您')->send();
        }catch (MsgException $exception){
            return JsonResponse::make()->statusCode(400)->status(false)->message($exception->getMessage())->send();

        }catch (QueryException $exception){
            return JsonResponse::make()->statusCode(400)->status(false)->message('系統異常錯誤【500001】')->send();

        }catch (\Exception $exception){
            return JsonResponse::make()->statusCode(400)->status(false)->message('系統異常錯誤【500002】')->send();

        }

    }
}
