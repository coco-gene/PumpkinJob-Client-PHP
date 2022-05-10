<?php
/**
 * Created by PhpStorm.
 * User: peterzhang
 * Date: 2022/5/10
 * Time: 2:16 PM
 */
namespace IO\Github\PumpkinJob;

class OpenAPIConstant {

    public static String $WEB_PATH = "/openApi";

    public static String $ASSERT = "/assert";

    /* ************* JOB 区 ************* */

    public static String $SAVE_JOB = "/saveJob";
    public static String $COPY_JOB = "/copyJob";
    public static String $FETCH_JOB = "/fetchJob";
    public static String $FETCH_ALL_JOB = "/fetchAllJob";
    public static String $QUERY_JOB = "/queryJob";
    public static String $DISABLE_JOB = "/disableJob";
    public static String $ENABLE_JOB = "/enableJob";
    public static String $DELETE_JOB = "/deleteJob";
    public static String $RUN_JOB = "/runJob";

    /* ************* Instance 区 ************* */

    public static String $STOP_INSTANCE = "/stopInstance";
    public static String $CANCEL_INSTANCE = "/cancelInstance";
    public static String $RETRY_INSTANCE = "/retryInstance";
    public static String $FETCH_INSTANCE_STATUS = "/fetchInstanceStatus";
    public static String $FETCH_INSTANCE_INFO = "/fetchInstanceInfo";
    public static String $QUERY_INSTANCE = "/queryInstance";

    /* ************* Workflow 区 ************* */

    public static String $SAVE_WORKFLOW = "/saveWorkflow";
    public static String $COPY_WORKFLOW = "/copyWorkflow";
    public static String $FETCH_WORKFLOW = "/fetchWorkflow";
    public static String $DISABLE_WORKFLOW = "/disableWorkflow";
    public static String $ENABLE_WORKFLOW = "/enableWorkflow";
    public static String $DELETE_WORKFLOW = "/deleteWorkflow";
    public static String $RUN_WORKFLOW = "/runWorkflow";
    public static String $SAVE_WORKFLOW_NODE = "/addWorkflowNode";

    /* ************* WorkflowInstance 区 ************* */

    public static String $STOP_WORKFLOW_INSTANCE = "/stopWfInstance";
    public static String $RETRY_WORKFLOW_INSTANCE = "/retryWfInstance";
    public static String $FETCH_WORKFLOW_INSTANCE_INFO = "/fetchWfInstanceInfo";
    public static String $MARK_WORKFLOW_NODE_AS_SUCCESS = "/markWorkflowNodeAsSuccess";
}