<?php
/**
 * Created by PhpStorm.
 * User: billge
 * Date: 16-6-22
 * Time: 上午11:38
 * @property userDAO $userDAO
 */
class loginAction extends baseAction
{
    public $login_url='/auth/oa/';
    public $oa_wsdl_url='http://login.oa.com/Services/passportService.asmx?WSDL';

    /**
     * 登录
     */
    public function action_index()
    {
        $username = $this->getPost('username');
        if ($user = $this->userDAO->filter(['name'=>$username])->find()){
            Person::get($user['id'])->login();
        } else {
            $id = $this->userDAO->add(['name'=>$username, 'registerTime'=>time()]);
            Person::get($id)->login();
        }
        if ($lastUrl = TXApp::$base->session->lastUrl){
            unset(TXApp::$base->session->lastUrl);
            TXApp::$base->request->redirect($lastUrl);
        } else {
            TXApp::$base->request->redirect('/');
        }
    }

    /**
     * oa登录
     * @param $ticket
     */
    public function action_oa($ticket)
    {
        if(TXApp::$base->person->exist())
            TXApp::$base->request->redirect('/');
        if ($ticket) {
            $client = new \SoapClient($this->oa_wsdl_url);
            $res=$client->DecryptTicket(['encryptedTicket'=>$ticket]);
            $rtx=$res->DecryptTicketResult->LoginName;

            if ($user = $this->userDAO->filter(['name'=>$rtx])->find()){
                Person::get($user['id'])->login();
            } else {
                $id = $this->userDAO->add(['name'=>$rtx, 'registerTime'=>time()]);
                Person::get($id)->login();
            }
            TXApp::$base->request->redirect('/');
        }
        else{
            $url=TXApp::$base->request->getBaseUrl(true).'/login/oa';
            TXApp::$base->request->redirect("http://login.oa.com/modules/passport/signin.ashx?url={$url}");
        }
    }
}