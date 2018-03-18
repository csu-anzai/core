pipeline {
        agent any
        stages {
                stage('Prepare') {

                        steps {
                                echo "echo from inner load test"
                        }
                }
        }
}
