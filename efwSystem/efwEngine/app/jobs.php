<?php


namespace efwEngine\app;


use efwEngine\crypto;

class jobs
{
    static function genJobData($job, $data, $credentials = "")
    {
        $jobData = ["job" => $job, "data" => $data];
        if ($credentials != "") {
            $jobData["credentials"] = $credentials;
        }
        $encrypt = crypto::encrypt(json_encode($jobData));
        return $encrypt;
    }

    static function genCredentialData($data)
    {
        return $data;
    }

    static function doJob($jobData)
    {
        $job = crypto::decrypt($jobData);

        $job = json_decode($job, true);
        if (isset($job["credentials"])) {
            foreach ($job["credentials"] as $mode => $credential) {
                switch ($mode) {
                    case "ip":
                        if ($credential == system::getUserIP()) {
                            return [false, "cretendialError"];
                        } else {
                            return [false, "credentialsError"];
                        }
                        break;
                }
            }
        }
        switch ($job["job"]) {
            default:
                return [false, "errCommand"];
                break;
        }
    }
}