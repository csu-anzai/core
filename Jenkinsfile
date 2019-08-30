#!groovy

@Library('art-shared@master') _

pipeline {
        agent none

        options {
            buildDiscarder(logRotator(numToKeepStr: '5', artifactNumToKeepStr: '5'))
        }

        triggers {
            pollSCM('H/5 * * * * ')
        }

        stages {

            stage('Build') {
                parallel {


                    stage ('slave php 7.3') {
                        agent {
                            label 'php 7.3'
                        }
                        steps {
                            checkout([
                                $class: 'GitSCM', branches: scm.branches, extensions: [[$class: 'RelativeTargetDirectory', relativeTargetDir: 'core']], userRemoteConfigs: scm.userRemoteConfigs
                            ])

                            withAnt(installation: 'Ant') {
                                sh "ant -buildfile core/_buildfiles/build.xml buildSqliteFast"
                            }
                            archiveArtifacts 'core/_buildfiles/packages/'
                        }
                        post {
                            always {
                                junit 'core/_buildfiles/build/logs/junit.xml'
                                step([$class: 'Mailer', notifyEveryUnstableBuild: true, recipients: emailextrecipients([[$class: 'CulpritsRecipientProvider'], [$class: 'RequesterRecipientProvider']])])
                                deleteDir()
                            }
                        }
                    }
                }

            }

        }
        post {
            failure {
                sendNotification 'FAILURE'
            }
            unstable {
                sendNotification 'UNSTABLE'
            }
            success {
                sendNotification 'SUCCESS'
            }
        }
    }
