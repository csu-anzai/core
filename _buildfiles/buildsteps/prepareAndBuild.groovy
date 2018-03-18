pipeline {
  agent any
  stages {
    stage("Inner") {
      steps {
        echo "hello from inside"
      }
     }
   }
}