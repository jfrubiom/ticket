<table class="table ">
                            <thead>
                                <tr>
                                    <th class="col-lg-3">Date</th>
                                    <th class="col-lg-4 visible-lg visible-xs">Issue</th>
                                    <th class="col-lg-2 visible-lg">Handled By</th>  
                                    <th class="col-lg-1 hidden-xs">Action</th>
                                    <th class="col-lg-2 hidden-xs">{{ trans('ticket::ticket.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $color='auto';
                                ?>
                                @foreach ($tickets as $ticket)
                                
                                <?php
                                    $critArr = $ticket->criticallity->toArray();
                                    //print_r($critArr);
                                    $crit = last($critArr);
                                    $criticallity = $crit['criticallity'];
                                    
                                    if(!empty($prioArr[$criticallity])){
                                        $color='#'.$prioArr[$criticallity];
                                    }
                                    ?>
                                <tr id="ticket-{{$ticket['id']}}" style="background-color:{{$color}};">
                                    <?php 
                                    $time = strtotime($ticket['ticket_date']);
                                    ?>
                                    <td >&nbsp;{{ date('jS M Y', $time) }} 
                                        [<?php echo \Carbon\Carbon::createFromTimeStamp($time)->diffForHumans(); ?>]
                                    </td>
                                    <td class="visible-xs visible-lg"><a href="javascript:;" onclick="loadDiscussion({{$ticket['id']}})">{{$ticket['title']}}</a></td>
                                     <?php
                                    $assignedTo = $ticket->raisedFor->email;
                                    ?>
                                    <td class="visible-lg">&nbsp;{{ $assignedTo }}</td>
                                   
                                    <td class="hidden-xs">NA</td>
                                    <?php
                                    $stArr = $ticket->status->toArray();
                                    $st = last($stArr);
                                    ?>
                                   
                                    
                                    <td class="visible-lg">&nbsp;{{$st['status']}}</td>
                                   
                                    
                                </tr>
                                @endforeach
                            </tbody>

                        </table>