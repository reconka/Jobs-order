 # Notes
  - It looks like we can have multiple solutions ACB and CAB can be good:
   ```
    a =>
    b => c
    c =>
  ```
  - This Jobs actually Graphs, with [Topological sorting](https://en.wikipedia.org/wiki/Topological_sorting) can solve this problem
  - I need a very light weight Framework, Laravel Zero would be a good choice
  - I have to cover  not only the circular dependencies, but missing dependecy as well
  For example
  ```
    a =>
    b => x
    c => y
  ```


